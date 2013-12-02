<div class="packageLoaItemRels form">
<?php echo $form->create('PackageLoaItemRel');?>
	<fieldset>
 		<legend><?php __('Edit PackageLoaItemRel');?></legend>
	<?php
		echo $form->input('packageLoaItemRelId');
		echo $form->input('packageId');
		echo $form->input('loaItemId');
		echo $form->input('loaItemGroupId');
		echo $form->input('priceOverride');
		echo $form->input('quantity');
		echo $form->input('noCharge');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('PackageLoaItemRel.packageLoaItemRelId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('PackageLoaItemRel.packageLoaItemRelId'))); ?></li>
		<li><?php echo $html->link(__('List PackageLoaItemRels', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
