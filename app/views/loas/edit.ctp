<?php
$this->pageTitle = 'Edit Client Loa';
$this->searchController = 'Clients';
$html->addCrumb('Clients', '/clients');
$html->addCrumb($text->truncate($this->data['Client']['name'], 15), '/clients/view/'.$this->data['Client']['clientId']);
$html->addCrumb('LOA\'s', '/clients/'.$this->data['Client']['clientId'].'/loas');
$html->addCrumb('LOA #'.$this->data['Loa']['loaId'], '/loas/view/'.$this->data['Loa']['loaId']);
$html->addCrumb('Edit');

$loa = $this->data;
?>
<?=$layout->blockStart('header');?>
<?= $html->link('<span><b class="icon"></b>Delete LOA</span>', array('action'=>'delete', $form->value('Loa.loaId')), array('class' => 'button del'), sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Loa.loaId')), false); ?>
<?=$layout->blockEnd();?>
<h2 class="title"><?= $loa['Client']['name'] ?> <?=$html2->c($loa['Client']['clientId'], 'Client Id:')?></h2>
<div class="loas form">
<?php echo $form->create('Loa');?>
	<fieldset>
 		<legend class="collapsible"><?php __('Edit Loa');?> <?=$html2->c($loa['Loa']['loaId'], 'LOA Id:')?></legend>
	<?php
		echo $form->input('loaId');
		echo $form->input('numEmailInclusions');
		echo $form->input('loaValue');
		echo $form->input('totalRemitted');
		echo $form->input('customerApprovalStatusId');
		echo $form->input('customerApprovalDate');
	?>
	<div class="controlset">
		<span class='label'>User Options</span><? echo $form->input('upgraded'); ?>
	</div>
	<?
		echo $form->input('loaNumberPackages');
		echo $form->input('cashPaid');
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('totalKept');
	?>
	</fieldset>
	<div class="buttonrow">
		<?php echo $form->end('Submit');?>
	</div>control
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related LOA Items');?></span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($loa['LoaItem'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Type'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Base Price'); ?></th>
		<th><?php __('Per Person'); ?></th>
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
			<td><?php echo $loaItem['loaItemTypeId'];?></td>
			<td><?php echo $loaItem['itemName'];?></td>
			<td><?php echo $number->currency($loaItem['itemBasePrice']); ?></td>
			<td><?php echo $loaItem['perPerson'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'loa_items', 'action'=>'view', $loaItem['loaItemId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'loa_items', 'action'=>'edit', $loaItem['loaItemId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'loa_items', 'action'=>'delete', $loaItem['loaItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loaItem['loaItemId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
<div class="actions">
	<ul>
		
		<li>
		<?php
		echo $html->link('Add new LOA item',
						'/loas/'.$loa['Loa']['loaId'].'/loa_items/add',
						array(
							'title' => 'Add Loa Item',
							'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
							'complete' => 'closeModalbox()'
							),
						null,
						false
						);
		?></li>
	</ul>
</div>
</div>
</div>

<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related LOA Tiers');?></span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($loa['RevenueModelLoaRel'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Tier #')?></th>
		<th><?php __('Revenue Model'); ?></th>
		<th><?php __('Expiration Criteria'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;		
		foreach ($loa['RevenueModelLoaRel'] as $tier):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $tier['tierNum']?></td>
			<td><?php echo $tier['RevenueModel']['revenueModelName'];?></td>
			<td><?php echo $tier['ExpirationCriterium']['expirationCriteriaName'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View Details', true), array('controller'=> 'loa_items', 'action'=>'edit', $loaItem['loaItemId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'loa_items', 'action'=>'delete', $loaItem['loaItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loaItem['loaItemId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
<div class="actions">
	<ul>
		
		<li>
		<?php
		echo $html->link('Add new LOA tier',
						'/loas/'.$loa['Loa']['loaId'].'/revenue_model_loa_rels/add',
						array(
							'title' => 'Add LOA Tier',
							'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
							'complete' => 'closeModalbox()'
							),
						null,
						false
						);
		?></li>
	</ul>
</div>
</div>
</div>