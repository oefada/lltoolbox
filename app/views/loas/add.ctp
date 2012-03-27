<?php
$this->pageTitle = $clientName.$html2->c($this->data['Loa']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']);
$this->set('clientId', $this->data['Loa']['clientId']);
?>
<h2 class="title">New LOA</h2>
<div class="loas form">
<?php echo $form->create('Loa');?>
	<fieldset>
		<div class="controlset4">
		<?
		echo $multisite->checkbox('Loa');
		?>
		</div>
	<?php
		echo $form->input('modifiedBy', array('type' => 'hidden', 'value' => $userDetails['username']));
		echo $form->input('clientId', array('type' => 'hidden'));
		echo $form->input('loaLevelId', array('label' => 'LOA Level'));
		echo $form->input('customerApprovalDate', array('empty' => true, 'label' => 'Package in Date', 'timeFormat' => '', 'minYear' => date('Y', strtotime('January 01, 2000')), 'maxYear' => date('Y', strtotime('+5 year'))));
		echo $form->input('loaMembershipTypeId', array('label' => 'Membership Type'));
		
		// ts echo $_SERVER['DOCUMENT_ROOT']; echo "<br />";
		// ts echo $form->input('date');
				
		// orig echo $form->input('startDate');
		// orig echo $form->input('endDate');
		
		// to try echo $form->input('startHour');
		// to try echo $form->input('endHour');
		
		// to try echo $form->input('startMinute');
		// to try echo $form->input('endMinute');
		
		echo $form->input('startDate',array('minYear' => date('Y', strtotime('January 01, 2000')), 'maxYear' => date('Y', strtotime('+5 year')), 'timeFormat' => ''));
		echo $form->input('endDate', array('minYear' => date('Y', strtotime('January 01, 2000')), 'maxYear' => date('Y', strtotime('+5 year')), 'timeFormat' => ''));
			
		echo '<div id="_LoaMembershipFee" style="padding:0px;">' . $form->input('membershipFee') . '</div>';
		echo '<div id="_LoaMembershipFeeEstimated" style="padding:0px;">' . $form->input('membershipFeeEstimated', array('label' => 'Estimated Fee')) . '</div>';
		echo '<div id="_LoaMembershipTotalPackages" style="padding:0px;">' . $form->input('membershipTotalPackages') . '</div>';
		echo '<div id="_LoaRetailValueFee" style="padding:0px;display:none;">' . $form->input('retailValueFee', array('label'=>'Retail Value Credit')) . '</div>';
		echo $form->input('loaNumberPackages', array('label' => 'Commission-Free Packages'));
		echo $form->input('numEmailInclusions');
		echo $form->input('customerApprovalStatusId', array('label' => 'Client Approval Status'));
		echo '<div class="controlset">'.$form->input('moneyBackGuarantee')."</div>";
		echo '<div class="controlset">'.$form->input('upgraded')."</div>";
		
		echo $form->input('currencyId');
		
		echo $form->input('accountExecutive');
		echo $form->input('accountManager');
		echo $form->input('accountTypeId', array('label' => 'Account Type'));
		
		echo $form->input('notes', array('label'=>'LOA Notes', 'id' => 'loaNotes', 'onKeyDown'=>'limitText(loaNotes, 300)', 'onKeyUp'=>'limitText(loaNotes, 300)'));
		echo $form->input('emailNewsletterDates', array('id' => 'emailNewsletterDates', 'onKeyDown'=>'limitText(emailNewsletterDates, 300)', 'onKeyUp'=>'limitText(emailNewsletterDates, 300)'));
		echo $form->input('homepageDates', array('id' => 'homepageDates', 'onKeyDown'=>'limitText(homepageDates, 300)', 'onKeyUp'=>'limitText(homepageDates, 300)'));
		echo $form->input('additionalMarketing', array('id' => 'additionalMarketing', 'onKeyDown'=>'limitText(additionalMarketing, 300)', 'onKeyUp'=>'limitText(additionalMarketing, 300)'));
		echo $form->input('commissionStructure', array('id' => 'commissionStructure', 'onKeyDown'=>'limitText(commissionStructure, 300)', 'onKeyUp'=>'limitText(commissionStructure, 300)'));
		
		echo $form->input('commissionFreeYield');
		echo $form->input('luxuryLinkFee');
		echo $form->input('familiyGetawayFee');
		echo $form->input('advertisingFee');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>



<script type="text/javascript">

jQuery(document).ready(function() {
	jQuery("#LoaAddForm").submit(function(){
		if (jQuery("#LoaSitesLuxuryLink").attr('checked')==false && jQuery("#LoaSitesFamily").attr('checked')==false){
			alert("You must check off which site(s) this is for.");
			return false;
		}else{
			return true;
		}
	});
});


Event.observe('LoaLoaMembershipTypeId', 'change', toggle_fields);
Event.observe(window, 'load', toggle_fields);
function toggle_fields() {
	if ($('LoaLoaMembershipTypeId').getValue() == 3) {
		$('_LoaMembershipFee').hide();
		$('_LoaMembershipTotalPackages').show();
		$('_LoaMembershipFeeEstimated').show();
		$('_LoaRetailValueFee').hide();
	} else if ($('LoaLoaMembershipTypeId').getValue() == 5) {
		$('_LoaMembershipFeeEstimated').show();
		$('_LoaRetailValueFee').show();
		$('_LoaMembershipTotalPackages').hide();
		$('_LoaMembershipFee').hide();
	} else {
		$('_LoaMembershipFee').show();
		$('_LoaMembershipTotalPackages').hide();
		$('_LoaMembershipFeeEstimated').hide();
		$('_LoaRetailValueFee').hide();
	}
}	

</script>
