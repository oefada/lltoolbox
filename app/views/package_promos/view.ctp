<div class="packagePromos view">
<h2><?php  __('PackagePromo');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackagePromoId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packagePromo['PackagePromo']['packagePromoId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packagePromo['PackagePromo']['packageId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packagePromo['PackagePromo']['description']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PromoCode'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packagePromo['PackagePromo']['promoCode']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit PackagePromo', true), array('action'=>'edit', $packagePromo['PackagePromo']['id'])); ?> </li>
		<li><?php echo $html->link(__('Delete PackagePromo', true), array('action'=>'delete', $packagePromo['PackagePromo']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $packagePromo['PackagePromo']['id'])); ?> </li>
		<li><?php echo $html->link(__('List PackagePromos', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New PackagePromo', true), array('action'=>'add')); ?> </li>
	</ul>
</div>
