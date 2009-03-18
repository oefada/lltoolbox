<div class="homepageMerchandisings view">
<h2><?php  __('HomepageMerchandising');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('HomepageMerchandisingId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('HomepageMerchandisingTypeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingTypeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $homepageMerchandising['HomepageMerchandising']['packageId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $homepageMerchandising['HomepageMerchandising']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LinkText'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $homepageMerchandising['HomepageMerchandising']['linkText']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LinkUrl'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $homepageMerchandising['HomepageMerchandising']['linkUrl']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Html'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $homepageMerchandising['HomepageMerchandising']['html']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit HomepageMerchandising', true), array('action'=>'edit', $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingId'])); ?> </li>
		<li><?php echo $html->link(__('Delete HomepageMerchandising', true), array('action'=>'delete', $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingId'])); ?> </li>
		<li><?php echo $html->link(__('List HomepageMerchandisings', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New HomepageMerchandising', true), array('action'=>'add')); ?> </li>
	</ul>
</div>
