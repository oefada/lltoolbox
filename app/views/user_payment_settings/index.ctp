<div class="userPaymentSettings index">
<h2><?php __('UserPaymentSettings');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('userPaymentSettingId');?></th>
	<th><?php echo $paginator->sort('ccNumber');?></th>
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('ccExpiration');?></th>
	<th><?php echo $paginator->sort('cvv2');?></th>
	<th><?php echo $paginator->sort('nameOnCard');?></th>
	<th><?php echo $paginator->sort('routingNumber');?></th>
	<th><?php echo $paginator->sort('accountNumber');?></th>
	<th><?php echo $paginator->sort('nameOnAccount');?></th>
	<th><?php echo $paginator->sort('paymentTypeId');?></th>
	<th><?php echo $paginator->sort('cc_year');?></th>
	<th><?php echo $paginator->sort('cc_month');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($userPaymentSettings as $userPaymentSetting):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['userPaymentSettingId']; ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['ccNumber']; ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['userId']; ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['ccExpiration']; ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['cvv2']; ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['nameOnCard']; ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['routingNumber']; ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['accountNumber']; ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['nameOnAccount']; ?>
		</td>
		<td>
			<?php echo $html->link($userPaymentSetting['PaymentType']['paymentTypeName'], array('controller'=> 'payment_types', 'action'=>'view', $userPaymentSetting['PaymentType']['paymentTypeId'])); ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['cc_year']; ?>
		</td>
		<td>
			<?php echo $userPaymentSetting['UserPaymentSetting']['cc_month']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $userPaymentSetting['UserPaymentSetting']['userPaymentSettingId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $userPaymentSetting['UserPaymentSetting']['userPaymentSettingId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $userPaymentSetting['UserPaymentSetting']['userPaymentSettingId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $userPaymentSetting['UserPaymentSetting']['userPaymentSettingId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New UserPaymentSetting', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Payment Types', true), array('controller'=> 'payment_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Type', true), array('controller'=> 'payment_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
