<div class="schedulingInstances view">
<h2><?php  __('SchedulingInstance');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SchedulingInstanceId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingInstance['SchedulingInstance']['schedulingInstanceId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SchedulingMasterId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingInstance['SchedulingInstance']['schedulingMasterId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('StartDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingInstance['SchedulingInstance']['startDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('EndDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingInstance['SchedulingInstance']['endDate']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit SchedulingInstance', true), array('action'=>'edit', $schedulingInstance['SchedulingInstance']['schedulingInstanceId'])); ?> </li>
		<li><?php echo $html->link(__('Delete SchedulingInstance', true), array('action'=>'delete', $schedulingInstance['SchedulingInstance']['schedulingInstanceId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $schedulingInstance['SchedulingInstance']['schedulingInstanceId'])); ?> </li>
		<li><?php echo $html->link(__('List SchedulingInstances', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New SchedulingInstance', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Offers', true), array('controller'=> 'offers', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer', true), array('controller'=> 'offers', 'action'=>'add')); ?> </li>
	</ul>
</div>
	<div class="related">
		<h3><?php  __('Related Offers');?></h3>
	<?php if (!empty($schedulingInstance['Offer'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('OfferId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $schedulingInstance['Offer']['offerId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SchedulingInstanceId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $schedulingInstance['Offer']['schedulingInstanceId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('OfferStatusId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $schedulingInstance['Offer']['offerStatusId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CurrencyExchangeRateId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $schedulingInstance['Offer']['currencyExchangeRateId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CurrencyExchangeRateField');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $schedulingInstance['Offer']['currencyExchangeRateField'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CreateDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $schedulingInstance['Offer']['createDate'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
				<li><?php echo $html->link(__('Edit Offer', true), array('controller'=> 'offers', 'action'=>'edit', $schedulingInstance['Offer']['offerId'])); ?></li>
			</ul>
		</div>
	</div>
	