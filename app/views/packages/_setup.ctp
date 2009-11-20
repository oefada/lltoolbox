<fieldset class="collapsible">
	<h3 class="handle">Setup</h3>
	<div class="collapsibleContent disableAutoCollapse">
		<?php
		echo $form->input('siteId', array('label' => 'Package For'));
		if ($this->data['Package']['internalApproval'] == 1) {
			echo $form->input('packageStatusId', array('label' => 'Status'));
		} else {
			echo $form->input('packageStatusId', array('label' => 'Status', 'disabled' => true));
		}
		
		echo "<div class='controlset'>";
		if (in_array("Merchandising", $userDetails['groups'])) {
			echo $form->input('internalApproval', array('label' => 'Approved By Merchandising'));
		} else {
			echo $form->input('internalApproval', array('label' => 'Approved By Merchandising', 'disabled' => true));
		}
		echo $form->input('isPrivatePackage', array('label' => '<span style="color: #990000">Is Private Package</span>'));
		echo $form->input('isTaxIncluded');
		echo "</div>";
		
		echo $form->input('currencyId', array('disabled' => 'true', 'label' => 'Currency'));
		echo $form->input('currencyId', array('type' => 'hidden'));
		if (!empty($this->data['Package']['copiedFromPackageId'])):
		echo "<label>Cloned from package</label>";
		echo $html->link($this->data['Package']['copiedFromPackageId'], "/clients/$clientId/packages/edit/".$this->data['Package']['copiedFromPackageId']);
		endif;
		echo $form->input('Package.packageName', array('label' => 'Working Name'));
?>

	<?php switch ($this->data['Package']['siteId']) {
			case 2: 	//Family
				$display_guests = 'style="display:none"';
				$display_all = '';
				break;
			case 1:	//Luxury Link
			default:	//site is null
				$display_guests = '';
				$display_all = 'style="display:none"';
				break;				
		}
	?>
	<div id="numGuests" <?php echo $display_guests; ?>>
	<?php echo $form->input('numGuests', array('label' => '# of Guests', 'style' => 'width: 50px;', 'after' => ' <a href="#" class="toggleGuestInput" onclick="return false;">or, enter separate number of adults and children</a>')); ?> 
	</div>
	<div id="numChildrenAdults" <?php echo $display_all ?>>
		<?php echo $form->input('numAdults', array('style' => 'width: 50px', 'label' => '# of Adults')); ?>
		<?php echo $form->input('numChildren', array('style' => 'width: 50px', 'label' => '# of Children', 'after' => '
		<a href="#" class="toggleGuestInput" onclick="return false;">or, enter total number of guests</a>')); ?>
	</div>
	
	<div class="input select" id="ageRangeValidity" <?php echo $display_all; ?>>
		<label>Valid for children ages:</label>
		<div style="float: left; clear:right;" class="clearfix">
	<?php 
		$i = 0;
		do {
			echo $this->renderElement('../packages/_age_range_row', array('row' => $i, 'data' => @$package['PackageAgeRange'][$i]));
		} while(isset($package['PackageAgeRange'][++$i]));
	?>
		<a href="#" id="addAnotherAgeRange" onclick="return false;">+ Add another age range</a>
		</div>
	</div>

<?php

		echo $form->input('roomGrade');
		
		if(count($clientLoaDetails) > 1):
			echo $form->input('numNights', array('readonly' => true));
		else: 
			echo $form->input('numNights');
		endif;
		?>
	<? /* -- BEGIN Validity and Blackouts -- */?>
		<div style="clear: both; ">
			<label><br />Validity</label>
			<div style="float: left; clear: none">
				<?php
				echo '<span class="label">Start</span>';
				echo $form->input('validityStartDate', array('label' => false));
				?>
			</div>
			<div style="float: left; clear: right">
				<?php
				echo '<span class="label">End</span>';
				echo $form->input('validityEndDate', array('label' => false));
				?>
			</div>
		</div>
		<div style="clear: both"></div>
		<?php echo $this->renderElement('../packages/_blackouts'); ?>
		
	<? /* -- END Validity and Blackouts -- */?>
	<div style="clear: both; margin: 0; padding: 0"></div>
	<? /* -- BEGIN Items and Rate Periods -- */?>
	<?php echo $this->renderElement('../packages/_items'); ?>
	<fieldset class="collapsible lastCollapsible">
		<h3 class="handle">Rate Periods</h3>
		<div class='collapsibleContent'>
			<div id='ratePeriods'>
				<?= $this->renderElement('../packages/package_rate_periods', array('packageRatePreview' => false)) ?>
			</div>
		</div>
	</fieldset>
	<? /* -- END Items and Rate Periods -- */?>
	
	<? /* -- BEGIN Retail Prices/Exchange Rate -- */ ?>
	<div style="float: left; clear: none">
	<?php
		echo $form->input('approvedRetailPrice', array('label' => 'Retail Value<br />'.$this->data['Currency']['currencyCode']));
	?>
	</div>
	<?php if ($this->data['Currency']['currencyCode'] != 'USD'): ?>
	<div style="float: left; clear: none">
	<?php
		echo "<label for='PackageApprovedRetailPriceExchangeRate'>Exchange Rate</label>";
		echo $form->select('approvedRetailPriceExchangeRate', array('1.2' => 'Today (1.2)', '1.5' => '7 Day Average (1.5)', '0.8' => '28 day average (0.8)'), null, array('label' => 'Exchange Rate'), false);
		echo $form->input('approvedRetailPriceInUSD', array('disabled' => 'disabled', 'label' => 'USD', 'value' => '$'.@$this->data['Package']['approvedRetailPrice']*1.2));
	?>
	</div>
	<script language="javascript">
	  var obs=new Form.Element.EventObserver($("PackageApprovedRetailPrice"),updateUsdPrice);
	  var obs2=new Form.Element.EventObserver($("PackageApprovedRetailPriceExchangeRate"),updateUsdPrice);
	  function updateUsdPrice(){
	     $('PackageApprovedRetailPriceInUSD').value = '$'+($F('PackageApprovedRetailPrice')*$F('PackageApprovedRetailPriceExchangeRate'));
	  }
	</script>
	<?php endif; //end exchange rate for non-us currency ?>
	<? /* -- END Retail Prices/Exchange Rate -- */?>
	<div style="clear: both"></div>
	<?php if (count($clientLoaDetails) == 1):?>
	<?php echo $form->input('reservePrice', array('label' => 'Guarantee Amount')); ?>
	<?php endif; ?>
	<?php echo $this->renderElement('../packages/_formats'); ?>
	
	<? /* -- BEGIN Scheduling Start/End Dates -- */?>
	<div style="float: left; clear: none">
	<?php
		echo $form->input('startDate', array('label' => 'Scheduling Range Start'));
	?>
	</div>
	<div style="float: left; clear: none">
	<?php
		echo $form->input('endDate', array('label' => 'Scheduling Range End'));
	?>
	</div>
	<? /* -- END Scheduling Start/End Dates -- */?>
	
	<?php
		echo $form->input('maxNumSales');
		echo $form->input('notes');
		echo $form->input('dateClientApproved', array('disabled' => true, 'empty' => true));
	?>
	</div>
</fieldset>

<script language="javascript">
	var storedNumAdults = '';
	var storedNumChildren = '';
	$$(".toggleGuestInput").invoke('observe', 'click', function(event) {
				var idOfContainer = event.element().up().up().id;
				
				if (idOfContainer == 'numChildrenAdults') {
					storedNumAdults = $("PackageNumAdults").value;
					storedNumChildren = $("PackageNumChildren").value;
					$("PackageNumAdults").value = '';
					$("PackageNumChildren").value = '';
				} else {
					$("PackageNumAdults").value = storedNumAdults;
					$("PackageNumChildren").value = storedNumChildren;
				}
				
				$('numChildrenAdults').toggle();
				$('numGuests').toggle();
				$('ageRangeValidity').toggle();
				
				return false;
				}
			);
	$("numChildrenAdults").observe('change', function() {$('PackageNumGuests').value = parseInt($F('PackageNumAdults')) + parseInt($F('PackageNumChildren'))});
	
	var numPackageAgeRanges = <?= $i ?>;
	
	$('addAnotherAgeRange').observe('click', function() {
		new Ajax.Request('/packages/age_range_row', {
		                method: 'get',
		                parameters: 'last=' + numPackageAgeRanges,
		                onLoading: function(){ $('spinner').show(); },
		                onSuccess: function(xhr){
							$('spinner').hide();
							numPackageAgeRanges = numPackageAgeRanges + 1;
		                	$('addAnotherAgeRange').insert({before : xhr.responseText});
							observeAgeRangeDel();
		                }
		                });
	});
	
	function observeAgeRangeDel() {
		$$('.ageRangeDel').invoke('observe', 'click', function(event) {
			div = event.element().up().up();	//remove the entire div

			div.remove();
		});
	}
	observeAgeRangeDel();
</script>
