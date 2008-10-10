<div class="loaItems view">
<h2><?php  __('LoaItem');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaItemId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItem['LoaItem']['loaItemId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ItemTypeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItem['LoaItemType']['loaItemTypeName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItem['LoaItem']['loaId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ItemName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItem['LoaItem']['itemName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ItemBasePrice'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItem['LoaItem']['itemBasePrice']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PerPerson'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItem['LoaItem']['perPerson']; ?>
			&nbsp;
		</dd>
	</dl>
</div>

<h3>Associated LOA Item Rate Periods</h3>
<div class="mB mT"><a href="/loaItems/<?php echo $loaItem['LoaItem']['loaItemId'];?>/loaItemRatePeriods/add">Add New LOA Item Rate Period</a></div>
<div class="mB">
	<table cellpadding="2" cellspacing="0">
	<tr>
		<th>Loa Item Rate Period Id</th>
		<th>Item Rate Period Name</th>
		<th>Start Date</th>
		<th>End Date</th>
		<th>Approved Retail Price</th>
		<th>Approved</th>
		<th>Approved By</th>
	</tr>
	<?php	
	foreach ($loaItem['LoaItemRatePeriod'] as $k=>$v) {
	?>
		<tr>
			<td><a href="/loaItemRatePeriods/edit/<?php echo $v['loaItemRatePeriodId'];?>"><?php echo $v['loaItemRatePeriodId'];?></td>
			<td><?php echo $v['loaItemRatePeriodName'];?></td>
			<td><?php echo $v['startDate'];?></td>
			<td><?php echo $v['endDate'];?></td>
			<td><?php echo $v['approvedRetailPrice'];?></td>
			<td><?php echo $v['approved'];?></td>
			<td><?php echo $v['approvedBy'];?></td>
		</tr>
	<?php
	}
	?>
	</table>
</div>

<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit LoaItem', true), array('action'=>'edit', $loaItem['LoaItem']['loaItemId'])); ?> </li>
		<li><?php echo $html->link(__('Delete LoaItem', true), array('action'=>'delete', $loaItem['LoaItem']['loaItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loaItem['LoaItem']['loaItemId'])); ?> </li>
		<li><?php echo $html->link(__('List LoaItems', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New LoaItem', true), array('action'=>'add')); ?> </li>
	</ul>
</div>
