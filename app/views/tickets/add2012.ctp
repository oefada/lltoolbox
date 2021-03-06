<div class="tickets form">
<?php echo $form->create(null, array('url' => '/tickets/add2012')); ?>
	<fieldset>
 		<legend><?php __('Create Manual Ticket');?></legend>
	<?php
		echo $form->input('manualTicketInitials', array('readonly' => 'readonly'));
		echo $form->input('ticketNotes');
		echo $form->input('siteIdSelecter', array('type'=>'select', 'label'=>'Site', 'empty'=>'--', 'options'=>array('1'=>'Luxury Link', '2'=>'Family', 'UK'=>'Luxury Link UK') ));
		echo $form->hidden('siteId');
        echo $form->hidden('tldId');
		echo $this->renderElement("input_search", array('name'=>'clientId', 'controller'=>'selectclients', 'label'=>'Client Id', 'style'=>'width:400px', 'multiSelect'=>'TicketClientId'));
		echo $form->input('packageId', array('type'=>'select', 'label'=>'Package Id', 'empty'=>'--', 'options'=>$packageList));
		echo $form->input('offerId', array('type'=>'select', 'label'=>'Offer Id', 'empty'=>'--', 'options'=>$offerList));
		echo $this->renderElement("input_search", array('name'=>'userId', 'controller'=>'users', 'label'=>'User Id', 'style'=>'width:400px', 'multiSelect'=>'TicketUserId')); ?>	
		
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
			<? echo $form->input('departureDisplay', array('type'=>'hidden')); ?>
			
			<span style="margin-left: 20px; color: #898989; font-weight: bold;">Request Departure</span>
			<span id="lblDeparture" style="margin-left: 10px;"><?= $this->data['Ticket']['departureDisplay']; ?></span>
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
			$("#TicketSiteIdSelecter").change(function(){
				
				var slct = $(this).val();
				if (slct == 'UK') {
					$('#TicketSiteId').val(1);
                    $('#TicketTldId').val(2);
				} else {
					$('#TicketSiteId').val(slct);
                    $('#TicketTldId').val(1);
				}
                tldPriceDisplay();

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
				
                var tld = $('#TicketTldId').val();
				
                $.getJSON("/tickets/mt_offerlist_ajax",{siteId: site, packageId: $(this).val(), tldId: tld}, function(data) {

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
                    
                    // var tldPrice = info[4].replace(' GBP', '');
                    // $('#TicketTldBillingPrice').val(tldPrice);
				}
			});

			$('#TicketRequestArrivalYear').change(function() {
				showDeparture();	
			});
			$('#TicketRequestArrivalMonth').change(function() {
				showDeparture();	
			});
			$('#TicketRequestArrivalDay').change(function() {
				showDeparture();	
			});
			$('#TicketNumNights').change(function() {
				showDeparture();	
			});
			
			function showDeparture() {
				var nights = parseInt($('#TicketNumNights').val());
				var display = '-';
				if (nights > 0) {
					var dtY = $('#TicketRequestArrivalYear').val();
					var dtM = $('#TicketRequestArrivalMonth').val();
					var dtD = $('#TicketRequestArrivalDay').val();
					if (dtY != '' && dtM != '' && dtD != '') {
						var dt = new Date(dtY, dtM - 1, dtD, 1, 0, 0, 0);
						dt.setDate(dt.getDate() + nights);
						display = dt.toLocaleDateString();
					}
				}
				$('#TicketDepartureDisplay').val(display);
				$('#lblDeparture').html(display);
			}
			
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

				if (site != '') {
				
                    var tld = $('#TicketTldId').val();
				
                    $.getJSON("/tickets/mt_packagelist_ajax",{siteId: site, clientId: client, tldId: tld}, function(data) {                  

						resetPackageList();

						for (package in data.packages) {
							if (data.packages.hasOwnProperty(package)) {
								 $('#TicketPackageId')[0].options.add(new Option(data.packages[package], package));
							}
						}
					})
				}
			}
            
            function tldPriceDisplay() {

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
