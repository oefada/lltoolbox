<div class="accolades view">
<h2><?php  __('Accolade');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AccoladeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $accolade['Accolade']['accoladeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ClientId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $accolade['Accolade']['clientId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Accolade Source'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($accolade['AccoladeSource']['accoladeSourceName'], array('controller'=> 'accolade_sources', 'action'=>'view', $accolade['AccoladeSource']['accoladeSourceId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AccoladeName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $accolade['Accolade']['accoladeName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $accolade['Accolade']['description']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AccoladeDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $accolade['Accolade']['accoladeDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DisplayDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $accolade['Accolade']['displayDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Inactive'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $accolade['Accolade']['inactive']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Accolade', true), array('action'=>'edit', $accolade['Accolade']['accoladeId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Accolade', true), array('action'=>'delete', $accolade['Accolade']['accoladeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $accolade['Accolade']['accoladeId'])); ?> </li>
		<li><?php echo $html->link(__('List Accolades', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Accolade', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Accolade Sources', true), array('controller'=> 'accolade_sources', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Accolade Source', true), array('controller'=> 'accolade_sources', 'action'=>'add')); ?> </li>
	</ul>
</div>
