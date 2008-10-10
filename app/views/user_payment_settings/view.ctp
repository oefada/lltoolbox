<div class="userPaymentSettings view">
<h2><?php  __('UserPaymentSetting');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserPaymentSettingId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['userPaymentSettingId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CcNumber'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['ccNumber']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['userId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CcExpiration'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['ccExpiration']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cvv2'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['cvv2']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NameOnCard'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['nameOnCard']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RoutingNumber'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['routingNumber']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AccountNumber'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['accountNumber']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NameOnAccount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['nameOnAccount']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Payment Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($userPaymentSetting['PaymentType']['paymentTypeName'], array('controller'=> 'payment_types', 'action'=>'view', $userPaymentSetting['PaymentType']['paymentTypeId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cc Year'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['cc_year']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cc Month'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $userPaymentSetting['UserPaymentSetting']['cc_month']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit UserPaymentSetting', true), array('action'=>'edit', $userPaymentSetting['UserPaymentSetting']['userPaymentSettingId'])); ?> </li>
		<li><?php echo $html->link(__('Delete UserPaymentSetting', true), array('action'=>'delete', $userPaymentSetting['UserPaymentSetting']['userPaymentSettingId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $userPaymentSetting['UserPaymentSetting']['userPaymentSettingId'])); ?> </li>
		<li><?php echo $html->link(__('List UserPaymentSettings', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New UserPaymentSetting', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Payment Types', true), array('controller'=> 'payment_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Type', true), array('controller'=> 'payment_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
