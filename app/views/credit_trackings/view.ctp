<div class="creditTrackings index">
<h2>Credit on File for User Id: <span style="color:black;"><?php echo $creditTrackings[0]['CreditTracking']['userId']; ?></span></h2>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>creditTrackingId</th>
	<th>Tracking Type</th>
	<th>User Id</th>
	<th>Offer Id</th>
	<th>Ticket Id</th>
	<th>Amount</th>
   	<th>Running Balance</th>
   	<th>Notes</th>
   	<th>Datetime</th>
</tr>
<?php
$i = 0;
foreach ($creditTrackings as $creditTracking):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $creditTracking['CreditTracking']['creditTrackingId']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTrackingType']['creditTrackingTypeName']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTracking']['userId']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTrackingOfferRel']['offerId']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTrackingTicketRel']['ticketId']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTracking']['amount']; ?>
		</td>
		<td>
			$<?php echo $creditTracking['CreditTracking']['balance']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTracking']['notes']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTracking']['datetime']; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>

<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List All Credit on File', true), array('action'=>'index')); ?></li>
	</ul>
</div>

<?



/*


<div class="creditTrackings view">
<h2><?php  __('CreditTracking');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CreditTrackingId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $creditTracking['CreditTracking']['creditTrackingId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CreditTrackingTypeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $creditTracking['CreditTracking']['creditTrackingTypeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $creditTracking['CreditTracking']['userId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Amount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $creditTracking['CreditTracking']['amount']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Balance'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $creditTracking['CreditTracking']['balance']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Notes'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $creditTracking['CreditTracking']['notes']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Datetime'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $creditTracking['CreditTracking']['datetime']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit CreditTracking', true), array('action'=>'edit', $creditTracking['CreditTracking']['creditTrackingId'])); ?> </li>
		<li><?php echo $html->link(__('Delete CreditTracking', true), array('action'=>'delete', $creditTracking['CreditTracking']['creditTrackingId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $creditTracking['CreditTracking']['creditTrackingId'])); ?> </li>
		<li><?php echo $html->link(__('List CreditTrackings', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New CreditTracking', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Credit Tracking Types', true), array('controller'=> 'credit_tracking_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Credit Tracking Type', true), array('controller'=> 'credit_tracking_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Credit Tracking Offer Rels', true), array('controller'=> 'credit_tracking_offer_rels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Credit Tracking Offer Rel', true), array('controller'=> 'credit_tracking_offer_rels', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Credit Tracking Ticket Rels', true), array('controller'=> 'credit_tracking_ticket_rels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Credit Tracking Ticket Rel', true), array('controller'=> 'credit_tracking_ticket_rels', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Credit Tracking Offer Rels');?></h3>
	<?php if (!empty($creditTracking['CreditTrackingOfferRel'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('CreditTrackingOfferRelId'); ?></th>
		<th><?php __('CreditTrackingId'); ?></th>
		<th><?php __('OfferId'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($creditTracking['CreditTrackingOfferRel'] as $creditTrackingOfferRel):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $creditTrackingOfferRel['creditTrackingOfferRelId'];?></td>
			<td><?php echo $creditTrackingOfferRel['creditTrackingId'];?></td>
			<td><?php echo $creditTrackingOfferRel['offerId'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'credit_tracking_offer_rels', 'action'=>'view', $creditTrackingOfferRel['creditTrackingOfferRelId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'credit_tracking_offer_rels', 'action'=>'edit', $creditTrackingOfferRel['creditTrackingOfferRelId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'credit_tracking_offer_rels', 'action'=>'delete', $creditTrackingOfferRel['creditTrackingOfferRelId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $creditTrackingOfferRel['creditTrackingOfferRelId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Credit Tracking Offer Rel', true), array('controller'=> 'credit_tracking_offer_rels', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Credit Tracking Ticket Rels');?></h3>
	<?php if (!empty($creditTracking['CreditTrackingTicketRel'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('CreditTrackingTicketRelId'); ?></th>
		<th><?php __('CreditTrackingId'); ?></th>
		<th><?php __('TicketId'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($creditTracking['CreditTrackingTicketRel'] as $creditTrackingTicketRel):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $creditTrackingTicketRel['creditTrackingTicketRelId'];?></td>
			<td><?php echo $creditTrackingTicketRel['creditTrackingId'];?></td>
			<td><?php echo $creditTrackingTicketRel['ticketId'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'credit_tracking_ticket_rels', 'action'=>'view', $creditTrackingTicketRel['creditTrackingTicketRelId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'credit_tracking_ticket_rels', 'action'=>'edit', $creditTrackingTicketRel['creditTrackingTicketRelId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'credit_tracking_ticket_rels', 'action'=>'delete', $creditTrackingTicketRel['creditTrackingTicketRelId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $creditTrackingTicketRel['creditTrackingTicketRelId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Credit Tracking Ticket Rel', true), array('controller'=> 'credit_tracking_ticket_rels', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>


*/
?>