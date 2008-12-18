<fieldset class="collapsible">
	<h3 class="handle">Setup</h3>
	<div class="collapsibleContent disableAutoCollapse">
		<?php
		echo $form->input('packageStatusId', array('label' => 'Status'));
		echo $form->input('currencyId', array('disabled' => 'true', 'label' => 'Currency'));
		echo $form->input('currencyId', array('type' => 'hidden'));
		if (!empty($this->data['Package']['copiedFromPackageId'])):
		echo "<label>Cloned from package</label>";
		echo $html->link($this->data['Package']['copiedFromPackageId'], "/clients/$clientId/packages/edit/".$this->data['Package']['copiedFromPackageId']);
		endif;
		echo $form->input('Package.packageName', array('label' => 'Working Name'));
		echo $form->input('numGuests');
		echo $form->input('numNights');
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
	<?php echo $form->input('reservePrice'); ?>
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
		echo $form->input('dateClientApproved');
	?>
	</div>
</fieldset>