<?php
$this->pageTitle = $clientName.$html2->c($this->data['Loa']['clientId'], 'Client Id:');
$this->set('clientId', $this->data['Loa']['clientId']);
?>
<h2 class="title">New LOA</h2>
<div class="loas form">
<?php echo $form->create('Loa');?>
	<fieldset>
	<?php
		echo $form->input('clientId', array('type' => 'hidden'));
		echo $form->input('loaLevelId', array('label' => 'LOA Level'));
		echo $form->input('loaMembershipTypeId', array('label' => 'Membership Type'));
		echo '<div id="_LoaMembershipFee" style="padding:0px;">' . $form->input('membershipFee') . '</div>';
		echo '<div id="_LoaMembershipFeeEstimated" style="padding:0px;">' . $form->input('membershipFeeEstimated', array('label' => 'Estimated Fee')) . '</div>';
		echo '<div id="_LoaMembershipTotalPackages" style="padding:0px;">' . $form->input('membershipTotalPackages') . '</div>';
		echo $form->input('loaNumberPackages', array('label' => 'Commission-Free Packages'));
		echo $form->input('numEmailInclusions');
		echo $form->input('customerApprovalStatusId', array('label' => 'Client Approval Status'));
		echo '<div class="controlset">'.$form->input('moneyBackGuarantee')."</div>";
		echo '<div class="controlset">'.$form->input('upgraded')."</div>";
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('currencyId');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>



<script type="text/javascript">

Event.observe('LoaLoaMembershipTypeId', 'change', toggle_fields);
Event.observe(window, 'load', toggle_fields);
function toggle_fields() {
	if ($('LoaLoaMembershipTypeId').getValue() == 3) {
		$('_LoaMembershipFee').hide();
		$('_LoaMembershipTotalPackages').show();
		$('_LoaMembershipFeeEstimated').show();
		
	} else {
		$('_LoaMembershipFee').show();
		$('_LoaMembershipTotalPackages').hide();
		$('_LoaMembershipFeeEstimated').hide();
	}
}	

</script>