<?php

$loa = $this->data;
echo $this->element("loas_subheader", array("loa"=>$loa,"client"=>$client));

$this->searchController = 'Clients';
$this->set('clientId', $this->data['Client']['clientId']);

echo $layout->blockStart('header');
echo $html->link('<span><b class="icon"></b>Delete LOA</span>', array(
	'action'=>'delete', 
	$form->value('Loa.loaId')), 
	array('class' => 'button del'), 
	sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Loa.loaId')), 
	false
); 
echo $layout->blockEnd();

?>

<style>
	div.pub-status div.checkbox input[type="checkbox"] {
		width:20px;
	}
</style>

<h2 class="title">
	<?php __('Edit Loa');
	echo $html2->c($loa['Loa']['loaId'], 'LOA Id:')?>
</h2>

<div class="loas form">

<?php 

echo $form->create('Loa');
echo $form->submit('Submit');
echo '<fieldset>';

// for editing membershipBalance, totalKept, totalRemitted, totalRevenue
$uname=$userDetails['username'];
$userGroupsArr=$userDetails['groups'];
$userPermArr=array('mchoe','dpen','kferson','mbyrnes','jlagraff','mtrinh');

$disable_advanced_edit = (in_array($uname,$userPermArr) || in_array('Production', $userGroupsArr)) ? false : true;

$userPermArr=array('dpen','kferson','mchoe','rfriedman','jlagraff','mtrinh','mbyrnes');
if (in_array($uname, $userPermArr) || in_array('Production', $userGroupsArr)) {
	$disabled = false;
} else {
	$disabled = true;
}

// for editing membershipPackagesRemaining
$userPermArr=array('mchoe','kferson','jlagraff','mtrinh','mbyrnes');
$disable_mp = (in_array($uname, $userPermArr) || in_array('Production', $userGgroupsArr)) ? false : true;

?>

<div style="clear:both;"></div>

<?php 

echo $form->input('loaLevelId', array('disabled' => $disabled, 'label' => 'LOA Level')); 
echo $form->input('startDate', array(
	'minYear' => date('Y', strtotime('January 01, 2000')), 
	'maxYear' => date('Y', strtotime('+5 year')), 'timeFormat' => '')
);
echo $form->input('endDate',  array(
	'minYear' => date('Y', strtotime('January 01, 2000')), 
	'maxYear' => date('Y', strtotime('+5 year')), 'timeFormat' => '')
);
echo $form->input('notes', array('label'=>'LOA Notes', 'id' => 'loaNotes'));
echo $form->input('customerApprovalDate', array(
		'empty' => true, 
		'label' => 'Package in Date', 
		'minYear' => date('Y', strtotime('January 01, 2000')), 
		'maxYear' => date('Y', strtotime('+5 year')), 'timeFormat' => ''
	)
);
echo $form->input('Loa.currencyId', array('label' => 'Item Currency'));
echo $form->input('accountExecutive');
echo $form->input('accountManager');
echo $form->input('accountTypeId', array('label' => 'Account Type')); 
echo '<div class="controlset4">'.$multisite->checkbox('Loa').'</div>';
echo $form->input('loaMembershipTypeId', array(
	'label' => 'Membership Type', 
	'disabled' => $disable_advanced_edit)
);
$enable_est = !$disable_advanced_edit && ($loa['Loa']['loaMembershipTypeId'] == 3) ? true : false;
echo $form->input('membershipFeeEstimated', array('disabled' => $enable_est));
echo $form->input('membershipTotalPackages');
echo $form->input('membershipPackagesRemaining', array('disabled' => $disable_mp));
echo $form->input('membershipFee', array('disabled' => $disabled));
echo $form->input('membershipBalance', array('disabled' => $disable_advanced_edit));
echo $form->input('membershipTotalNights');
echo $form->input('membershipNightsRemaining');
$enable_rvc = !$disable_advanced_edit && ($loa['Loa']['loaMembershipTypeId'] == 5) ? true : false;
echo $form->input('retailValueBalance', array('disabled' => $enable_rvc));
echo $form->input('retailValueFee', array('disabled' => $enable_rvc));
echo $form->input('totalRevenue', array('disabled' => $disable_advanced_edit, 'label' => 'Total Revenue'));
echo $form->input('totalRemitted', array('disabled' => $disable_advanced_edit));
if (in_array($uname,array('kferson', 'jlagraff','mtrinh', 'mbyrnes'))) {
	echo $form->input('cashPaid');
} else {
	echo $form->input('cashPaid', array('disabled' => true));
}
echo $form->input('totalKept', array('disabled' => $disable_advanced_edit));
echo $form->input('totalCommission', array('disabled' => $disable_advanced_edit));
echo $form->input('renewalResult', array('type' => 'select', 'options' => $renewalResultOptions));
echo $form->input('nonRenewalNote', array('type' => 'textarea'));
echo $form->input('nonRenewalReason', array('type' => 'select', 'options' => $nonRenewalReasonOptions));
echo $form->input('luxuryLinkFee');
echo $form->input('familyGetawayFee');
echo $form->input('advertisingFee');

?>

<div class="controlset">
<span class='label'>&nbsp;</span>

<?php

echo $form->input('moneyBackGuarantee', array('label' => 'Money Back Guarantee'));
echo $form->input('upgraded', array('label'=>'Risk Free Guarantee'));
//echo $form->input('inactive');

echo '<div class="controlset" style="margin-left:-155px;">';
echo $form->input('checkboxes', array(
		'label'=>false,
		'type'=>'select',
		'multiple'=>'checkbox',
		'options'=>$checkboxValuesArr,
		'selected'=>$checkboxValuesSelectedArr
	)
);
echo '</div></div>';

echo $form->input('numEmailInclusions');
echo $form->input('auctionCommissionPerc', array('label'=>'Auction % Commission'));
echo $form->input('buynowCommissionPerc', array('label'=>'BuyNow % Commission'));
echo $form->input('emailNewsletterDates', array(
		'label'=>'Packaging Notes', 
		'id' => 'emailNewsletterDates', 
		'onKeyDown'=>'limitText(emailNewsletterDates, 300)', 
		'onKeyUp'=>'limitText(emailNewsletterDates, 300)'
	)
);
echo $form->input('homepageDates', array(
		'label'=>'Homepage Placements', 
		'id' => 'homepageDates', 
		'onKeyDown'=>'limitText(homepageDates, 300)', 
		'onKeyUp'=>'limitText(homepageDates, 300)'
	)
);
echo $form->input('additionalMarketing', array(
		'id' => 'additionalMarketing', 
		'onKeyDown'=>'limitText(additionalMarketing, 300)', 
		'onKeyUp'=>'limitText(additionalMarketing, 300)'
	)
);
echo $form->input('loaNumberPackages', array('label' => 'Commission-Free Packages'));
echo $form->input('loaId');
echo $form->input('clientId', array('type' => 'hidden'));

if (isset($loa['Loa']['created'])){
	echo '<div><label>Created</label><span>';
	echo $loa['Loa']['created'];
	echo '</span></div>';
}
if (isset($loa['Loa']['modified'])){
	echo '<div><label>Modified</label><span>'.$loa['Loa']['modified'].'</span></div>';
	echo '<div><label>Modified By</label><span>'.$loa['Loa']['modifiedBy'].'</span></div>';
}
?>
</fieldset>
	<div class="buttonrow">
		<?php echo $form->end('Submit');?>
	</div>
</div>

<div class="collapsible">
	<div class="handle"><?php __('Related LOA Tracks');?></div>
	<div class="collapsibleContent related">
		<div class="actions">
			<ul>

				<li>
				<?php

				echo $html->link('Add new LOA track',
								'/loas/'.$loa['Loa']['loaId'].'/tracks/add/',
								array(
									'title' => 'Add LOA Track',
									'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
									'complete' => 'closeModalbox()'
									),
								null,
								false
								);
				?></li>
			</ul>
		</div>
	<?php if (!empty($loa['Track'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Track Name')?></th>
		<th><?php __('Revenue Model'); ?></th>
		<th><?php __('Expiration Criteria'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($loa['Track'] as $track):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $track['trackName']?></td>
			<td><?php echo $track['RevenueModel']['revenueModelName'];?></td>
			<td><?php echo $track['ExpirationCriterium']['expirationCriteriaName'];?></td>
			<td class="actions">
				<?php
				echo $html->link('Edit',
								'/tracks/edit/'.$track['trackId'],
								array(
									'title' => 'Edit LOA Track',
									'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
									'complete' => 'closeModalbox()'
									),
								null,
								false
								);
				?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'tracks', 'action'=>'delete', $track['trackId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $track['trackId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>
</div>
<script type="text/javascript">

jQuery(document).ready(function() {
	jQuery("#LoaEditForm").submit(function(){

		if (jQuery("#LoaMembershipBalance").val()==0 && <?=$form->data['Loa']['membershipBalance']?>>0){
			if (confirm('are you sure you want to set the membership balance to ZERO?')==false){
				return false;
			}
		}

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
		// # packages
		$('LoaMembershipTotalPackages').enable();
		$('LoaRetailValueFee').disable();
		$('LoaRetailValueBalance').disable();
		$('LoaMembershipFeeEstimated').enable();
	} else if ($('LoaLoaMembershipTypeId').getValue() == 5) {
		// retail value credit
		$('LoaMembershipTotalPackages').disable();
		$('LoaRetailValueFee').enable();
		$('LoaRetailValueBalance').enable();
		$('LoaMembershipFeeEstimated').enable();
	} else {
		$('LoaMembershipTotalPackages').disable();
		$('LoaRetailValueFee').disable();
		$('LoaRetailValueBalance').disable();
		$('LoaMembershipFeeEstimated').disable();
	}
}	

</script>
