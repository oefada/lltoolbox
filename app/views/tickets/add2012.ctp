<div class="tickets form">
<?php echo $form->create(null, array('url' => '/tickets/add2012')); ?>
	<fieldset>
 		<legend><?php __('Create Manual Ticket');?></legend>
	<?php
		echo $form->input('manualTicketInitials', array('readonly' => 'readonly'));
		echo $form->input('ticketNotes');
		echo $form->input('siteId');
		echo $this->renderElement("input_search", array('name'=>'clientId', 'controller'=>'selectclients', 'label'=>'Client Id', 'style'=>'width:400px', 'callingId'=>'tickeAdd2012'));
		echo $form->input('packageId', array('type'=>'select', 'label'=>'Package Id', 'empty'=>'--', 'options'=>$packageList));
		echo $form->input('offerId', array('type'=>'select', 'label'=>'Offer Id', 'empty'=>'--', 'options'=>$offerList));
		echo $this->renderElement("input_search", array('name'=>'userId', 'controller'=>'users', 'label'=>'User Id', 'style'=>'width:400px', 'callingId'=>'tickeAdd2012')); ?>	
		
		<div class="input select">
			<? echo $form->input('userPaymentSettingId', array('type'=>'select', 'div'=> false, 'label'=>'Credit Card', 'empty'=>'--', 'options'=>$ccList)); ?>
			&nbsp;&nbsp;&nbsp;<a id="btnCcEdit" href="javascript:void(0);">EDIT CARDS</a>
			&nbsp;&nbsp;&nbsp;<a id="btnCcRefresh" href="javascript:void(0);">REFRESH CARDS</a>
		</div>
		
	<?		
		echo $form->input('billingPrice');
		echo $form->input('offerPrice', array('type'=>'hidden'));
		echo $form->input('numNights');
		echo $form->input('offerNights', array('type'=>'hidden'));
	?>	
		<div class="input date">
			<? echo $form->input('requestArrival', array('div'=> false)); ?>
			<!--
			<span style="margin-left: 20px; color: #898989; font-weight: bold;">Request Departure</span>
			<span id="lblDeparture" style="margin-left: 10px;"><?= $this->data['Ticket']['departure']; ?></span>
			-->
		</div>
		
	<? 
		echo $form->input('promoCode');
		echo $form->input('requestNumGuests');
		echo $form->input('requestNotes');
		echo $form->input('autoConfirm', array('type'=>'select', 'empty'=>'--', 'options'=>array('Y'=>'Yes', 'N'=>'No')));
	?>

		<?php 
			echo $javascript->link('jquery/jquery',true);
			echo $javascript->link('jquery/jquery-noconflict',true);				  
		?>
		<script>	
		jQuery(function($) {
			$("#TicketSiteId").change(function(){
				var client = parseInt($('#TicketClientId').val());
				if (client > 0) {
					getPackageList();
				}
			});

			$("#TicketClientId").change(function(){
				var client = parseInt($('#TicketClientId').val());
				if (client > 0) {
					getPackageList();
				}
			});

			$("#TicketPackageId").change(function(){
				var site = $('#TicketSiteId').val();
				$('#TicketOfferId')[0].options.length = 0;
				$('#TicketOfferId')[0].options.add(new Option('--', ''));

				$.getJSON("/tickets/mt_offerlist_ajax",{siteId: site, packageId: $(this).val()}, function(data) {

					for (offer in data.offers) {
						if (data.offers.hasOwnProperty(offer)) {
							$('#TicketOfferId')[0].options.add(new Option(data.offers[offer], offer));
						}
					}
				})
			});

			$("#TicketOfferId").change(function(){
				if ($(this).val() != '') {
					var txt = $(this).find("option:selected").text();
					var info = txt.split(' : '); 
					
					var price = info[3].replace('$', '');
					$('#TicketBillingPrice').val(price);
					$('#TicketOfferPrice').val(price);
					
					var nights = info[1].replace(' nights', '');
					$('#TicketNumNights').val(nights);
					$('#TicketOfferNights').val(nights);										

					var guests = info[2].replace(' guests', '');
					$('#TicketRequestNumGuests').val(guests);
				}
			});
			
			function resetPackageList() {
				$('#TicketPackageId')[0].options.length = 0;
				$('#TicketOfferId')[0].options.length = 0;
				$('#TicketPackageId')[0].options.add(new Option('--', ''));
				$('#TicketOfferId')[0].options.add(new Option('--', ''));
			}

			function getPackageList() {
				var site = $('#TicketSiteId').val();
				var client = $('#TicketClientId').val();

				resetPackageList();

				$.getJSON("/tickets/mt_packagelist_ajax",{siteId: site, clientId: client}, function(data) {					

					resetPackageList();

					for (package in data.packages) {
						if (data.packages.hasOwnProperty(package)) {
							 $('#TicketPackageId')[0].options.add(new Option(data.packages[package], package));
						}
					}
				})
			}

			$("#TicketUserId").change(function(){
				$('#TicketUserPaymentSettingId')[0].options.length = 0;
				$('#TicketUserPaymentSettingId')[0].options.add(new Option('--', ''));

				if ($(this).val()) {
					$.getJSON("/tickets/mt_cclist_ajax",{userId: $(this).val()}, function(data) {

						$('#TicketUserPaymentSettingId')[0].options.length = 0;
						$('#TicketUserPaymentSettingId')[0].options.add(new Option('--', ''));

						for (cc in data.ccs) {
							if (data.ccs.hasOwnProperty(cc)) {
								$('#TicketUserPaymentSettingId')[0].options.add(new Option(data.ccs[cc], cc));
							}
						}
					})
				}
			});

			$("#btnCcEdit").click(function(){
				window.open('/users/edit/' + $("#TicketUserId").val());
			});

			$("#btnCcRefresh").click(function(){
				$("#TicketUserId").change();
			});

		});
		</script>
	</fieldset>
	<div class="submit">
		<input type="submit" value="Create Ticket" />
		<a href="/tickets" style="margin-left: 40px;">CANCEL ENTRY</a>
	</div>
</div>
