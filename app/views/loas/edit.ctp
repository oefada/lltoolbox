<?php
$loa = $this->data;
$this->pageTitle = $loa['Client']['name'].$html2->c($loa['Client']['clientId'], 'Client Id:');
$this->searchController = 'Clients';
$this->set('clientId', $this->data['Client']['clientId']);
?>
<?=$layout->blockStart('header');?>
<?= $html->link('<span><b class="icon"></b>Delete LOA</span>', array('action'=>'delete', $form->value('Loa.loaId')), array('class' => 'button del'), sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Loa.loaId')), false); ?>
<?=$layout->blockEnd();?>
<h2 class="title"><?php __('Edit Loa');?> <?=$html2->c($loa['Loa']['loaId'], 'LOA Id:')?></h2>
<div class="loas form">
<?php echo $form->create('Loa');?>
	<fieldset>
	<?php
		echo $form->input('loaId');
		
		//only tmccord can edit loa level
		if ($userDetails['username'] == 'tmccord') {
			$disabled = false;
		} else {
			$disabled = true;
		}
		echo $form->input('loaLevelId', array('disabled' => $disabled, 'label' => 'LOA Level'));
		echo $form->input('cash', array('type' => 'select', 'options' => array(0 => 'Barter', 1 => 'Cash')));
		echo $form->input('numEmailInclusions');
		echo $form->input('loaValue', array('disabled' => true, 'label' => 'Total Revenue'));
		echo $form->input('Loa.currencyId', array('label' => 'Item Currency'));
		echo $form->input('customerApprovalStatusId', array('label' => 'Client Approval Status'));
		echo $form->input('customerApprovalDate', array('label' => 'Package in Date', 'disabled' => true, 'empty' => true));
	?>
	<div class="controlset">
		<span class='label'>&nbsp;</span>
		<? echo $form->input('upgraded');
		 ?>
	</div>
	<?
		echo $form->input('loaNumberPackages', array('label' => 'Commission-Free Packages'));
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('membershipFee', array('disabled' => $disabled));
		echo $form->input('membershipBalance', array('disabled' => true));
		echo $form->input('totalRemitted', array('disabled' => true));
		
		if ($userDetails['username'] == 'cyoung') {
			echo $form->input('cashPaid');
		} else {
			echo $form->input('cashPaid', array('disabled' => true));
		}
		
		echo $form->input('totalKept', array('disabled' => true));
	?>
	</fieldset>
	<div class="buttonrow">
		<?php echo $form->end('Submit');?>
	</div>
</div>
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

<div class="collapsible">
	<div class="handle"><?php __('Related LOA Tracks');?></div>
	<div class="collapsibleContent related">
		<div class="actions">
			<ul>

				<li>
				<?php
				echo $html->link('Add new LOA track',
								'/loas/'.$loa['Loa']['loaId'].'/tracks/add',
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