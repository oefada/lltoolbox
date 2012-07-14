<?php
$loa = $this->data;
echo $this->element("loas_subheader", array("loa"=>$loa,"client"=>$client));

$this->searchController = 'Clients';
$this->set('clientId', $this->data['Client']['clientId']);
?>
<?=$layout->blockStart('header');?>
<?= $html->link('<span><b class="icon"></b>Delete LOA</span>', array('action'=>'delete', $form->value('Loa.loaId')), array('class' => 'button del'), sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Loa.loaId')), false); ?>
<?=$layout->blockEnd();?>
<style>
    div.pub-status div.checkbox input[type="checkbox"] {
        width:20px;
    }
</style>
<h2 class="title"><?php __('Edit Loa');?> <?=$html2->c($loa['Loa']['loaId'], 'LOA Id:')?></h2>
<div class="loas form">
<?php echo $form->create('Loa');?>
	<fieldset>
		<div class="controlset4">
		<?
		echo $multisite->checkbox('Loa');
		?>
		</div>
	<?php
		echo $form->input('loaId');
		if (in_array($userDetails['username'], array('alee','dpen','kferson','kgathany','mchoe','rfriedman','acarney','jlagraff','mtrinh')) ||
            in_array('Production', $userDetails['groups'])) {
			$disabled = false;
		} else {
			$disabled = true;
		}
		
		// for editing membershipBalance, totalKept, totalRemitted, totalRevenue
		// added Dec 22,2009 ALEE
		$disable_advanced_edit = (in_array($userDetails['username'], array('mchoe','dpen','kferson','mbyrnes','jlagraff','mtrinh')) ||
            in_array('Production', $userDetails['groups'])) ? false : true;

		// for editing membershipPackagesRemaining
		$disable_mp = (in_array($userDetails['username'], array('alee','mchoe','rhastings','kferson','acarney','jlagraff','mtrinh')) ||
            in_array('Production', $userDetails['groups'])) ? false : true;

		echo $form->input('clientId', array('type' => 'hidden'));
		echo $form->input('loaLevelId', array('disabled' => $disabled, 'label' => 'LOA Level')); ?>
		<div style="clear:both;"></div>
        <div class="pub-status" style="width:950px; clear:none; float: left; border: #CCCCCC solid 1px;">
        <label for="LoaPublishingStatus">Publishing Status</label>
		<input type="hidden" value="" name="data[Loa][PublishingStatusLL]" />
		<br />
            <div class="input select" style="width: 450px; float:left; clear:none;">
				<label for="LoaPublishingStatusLL" style="padding-left:70px; padding-bottom:10px;">Luxury Link</label>
				<input type="hidden" name="data[Loa][modifiedBy]" value="<?=$userDetails['username'];?>" />
                <input type="hidden" value="" name="data[Loa][PublishingStatusLL]" />
                <?php foreach ($publishingStatus as $pStatusId => $pStatus	): ?>
					<div class="checkbox">
						<input type="checkbox" id="LoaPublishingStatusLL<?php echo $pStatusId; ?>" value="<?php echo $pStatusId; ?>" name="data[Loa][PublishingStatusLL][]" <?php if (in_array($pStatusId, array_keys($completedStatusLL))) echo "checked"?>/>
						<label for="LoaPublishingStatusLL<?php echo $pStatusId; ?>"><?php echo $pStatus; ?></label>
						<?php if (in_array($pStatusId, array_keys($completedStatusLL))) echo '<span>'.date('M d, Y h:i a', strtotime($completedStatusLL[$pStatusId])).'</span>'; ?>
					</div>
                <?php endforeach; ?>
            </div>
            <div class="input select" style="width: 450px; float:right; clear:none;">
                <label for="LoaPublishingStatusFG" style="padding-left:70px; padding-bottom:10px;">Family Getaway</label>
                <input type="hidden" value="" name="data[Loa][PublishingStatusFG]" />
				<input type="hidden" name="data[Loa][modifiedBy]" value="<?=$userDetails['username'];?>" />
                <?php foreach ($publishingStatus as $pStatusId => $pStatus): ?>
					<div class="checkbox">
						<input type="checkbox" id="LoaPublishingStatusFG<?php echo $pStatusId; ?>" value="<?php echo $pStatusId; ?>" name="data[Loa][PublishingStatusFG][]" <?php if (in_array($pStatusId, array_keys($completedStatusFG))) echo "checked"; ?> />
						<label for="LoaPublishingStatusFG<?php echo $pStatusId; ?>"><?php echo $pStatus; ?></label>
						<?php if (in_array($pStatusId, array_keys($completedStatusFG))) echo '<span>'.date('M d, Y h:i a', strtotime($completedStatusFG[$pStatusId])).'</span>'; ?>
					</div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php   echo $form->input('customerApprovalDate', array('empty' => true, 'label' => 'Package in Date', 'minYear' => date('Y', strtotime('January 01, 2000')), 'maxYear' => date('Y', strtotime('+5 year')), 'timeFormat' => ''));
        		echo $form->input('loaMembershipTypeId', array('label' => 'Membership Type', 'disabled' => $disable_advanced_edit));
                echo $form->input('numEmailInclusions');
                echo $form->input('totalRevenue', array('disabled' => $disable_advanced_edit, 'label' => 'Total Revenue'));
                echo $form->input('Loa.currencyId', array('label' => 'Item Currency'));
                echo $form->input('customerApprovalStatusId', array('label' => 'Client Approval Status'));
        ?>
    
	<div class="controlset">
		<span class='label'>&nbsp;</span>
		<? echo $form->input('upgraded');
		 ?>
		<? echo $form->input('inactive');
		 ?>
		<? echo $form->input('moneyBackGuarantee', array('label' => 'Money Back Guarantee'));
		 ?>
	</div>
	<?

		echo $form->input('membershipTotalNights');
		echo $form->input('membershipNightsRemaining');

		echo $form->input('membershipTotalPackages');
		echo $form->input('membershipPackagesRemaining', array('disabled' => $disable_mp));
		echo $form->input('loaNumberPackages', array('label' => 'Commission-Free Packages'));
		echo $form->input('startDate', array('minYear' => date('Y', strtotime('January 01, 2000')), 'maxYear' => date('Y', strtotime('+5 year')), 'timeFormat' => ''));
		echo $form->input('endDate',  array('minYear' => date('Y', strtotime('January 01, 2000')), 'maxYear' => date('Y', strtotime('+5 year')), 'timeFormat' => ''));

		// ESTIMATED
		$enable_est = !$disable_advanced_edit && ($loa['Loa']['loaMembershipTypeId'] == 3) ? true : false;
		echo $form->input('membershipFeeEstimated', array('disabled' => $enable_est));	

		// RVC
		$enable_rvc = !$disable_advanced_edit && ($loa['Loa']['loaMembershipTypeId'] == 5) ? true : false;
		echo $form->input('retailValueBalance', array('disabled' => $enable_rvc));
		echo $form->input('retailValueFee', array('disabled' => $enable_rvc));
		echo $form->input('membershipFee', array('disabled' => $disabled));
		echo $form->input('membershipBalance', array('disabled' => $disable_advanced_edit));
		echo $form->input('totalRemitted', array('disabled' => $disable_advanced_edit));
		
		if (in_array($userDetails['username'],array('kferson', 'jlagraff','mtrinh'))) {
			echo $form->input('cashPaid');
		} else {
			echo $form->input('cashPaid', array('disabled' => true));
		}
		
		echo $form->input('totalKept', array('disabled' => $disable_advanced_edit));
		echo $form->input('totalCommission', array('disabled' => $disable_advanced_edit));
		
		echo $form->input('accountExecutive');
		echo $form->input('accountManager');
		
		echo $form->input('accountTypeId', array('label' => 'Account Type'));
		
		echo $form->input('notes', array('label'=>'LOA Notes', 'id' => 'loaNotes'));
		echo $form->input('emailNewsletterDates', array('id' => 'emailNewsletterDates', 'onKeyDown'=>'limitText(emailNewsletterDates, 300)', 'onKeyUp'=>'limitText(emailNewsletterDates, 300)'));
		echo $form->input('homepageDates', array('id' => 'homepageDates', 'onKeyDown'=>'limitText(homepageDates, 300)', 'onKeyUp'=>'limitText(homepageDates, 300)'));
		echo $form->input('additionalMarketing', array('id' => 'additionalMarketing', 'onKeyDown'=>'limitText(additionalMarketing, 300)', 'onKeyUp'=>'limitText(additionalMarketing, 300)'));
		echo $form->input('commissionStructure', array('id' => 'commissionStructure', 'onKeyDown'=>'limitText(commissionStructure, 300)', 'onKeyUp'=>'limitText(commissionStructure, 300)'));
		
		echo $form->input('commissionFreeYield');
		echo $form->input('luxuryLinkFee');
		echo $form->input('familiyGetawayFee');
		echo $form->input('advertisingFee');
		
		echo $form->input('renewalResult', array('type' => 'select', 'options' => $renewalResultOptions));
		echo $form->input('nonRenewalNote');
		echo $form->input('nonRenewalReason', array('type' => 'select', 'options' => $nonRenewalReasonOptions));
		
		
		echo '<div><label>Created</label><span>'.$loa['Loa']['created'].'</span></div>';
		echo '<div><label>Modified</label><span>'.$loa['Loa']['modified'].'</span></div>';
		echo '<div><label>Modified By</label><span>'.$loa['Loa']['modifiedBy'].'</span></div>';
		
	?>
	</fieldset>
	<div class="buttonrow">
		<?php echo $form->end('Submit');?>
	</div>
</div>

<!--
<div class="collapsible">
	<div class="handle"><?php __('Related LOA Items');?></div>
	<div class="related collapsibleContent">
		<div style="float: right">
		<?php
		echo $html->link('<span><b class="icon"></b>Add Loa Item</span>',
						'/loas/'.$loa['Loa']['loaId'].'/loa_items/add',
						array(
							'title' => 'Add Loa Item',
							'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
							'complete' => 'closeModalbox()',
							'class' => 'button add'
							),
						null,
						false
						);
		?>
		</div>
	<?php if (!empty($loa['LoaItem'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Type'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Live Site Description'); ?></th>
		<th><?php __('Base Price'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;		
		foreach ($loa['LoaItem'] as $loaItem):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $loaItem['LoaItemType']['loaItemTypeName'];?></td>
			<td><?php echo $loaItem['itemName'];?></td>
			<td><?php echo $text->excerpt($loaItem['merchandisingDescription'], 100);?></td>
			<td><?php echo $number->currency($loaItem['itemBasePrice'], $currencyCodes[$loaItem['currencyId']]); ?>
			<?php if (!empty($loaItem['Fee']['feePercent'])) {
				echo '+'.$loaItem['Fee']['feePercent'].'%';
			}?>	
				
			</td>
			<td class="actions">
				<?php echo $html->link('Edit',
								'/loa_items/edit/'.$loaItem['loaItemId'],
								array(
									'title' => 'Edit Loa Item',
									'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
									'complete' => 'closeModalbox()'
									),
								null,
								false
								); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'loa_items', 'action'=>'delete', $loaItem['loaItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loaItem['loaItemId'])); ?>
			</td>
		</tr>
		<tr<?php echo$class;?>>
			<td colspan='5' style="text-align: left; padding: 5px 20px" id='relatedLoaItemRatePeriods_<?=$loaItem['loaItemId']?>'>
				<?= $this->renderElement('loa_item_rate_periods/table_for_loas_page', array('loaItem' => $loaItem, 'closed' => true))?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
</div>
-->

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

		if (jQuery("#LoaMembershipBalance").val()==0 && <?=$client['Loa'][0]['membershipBalance']?>>0){
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

