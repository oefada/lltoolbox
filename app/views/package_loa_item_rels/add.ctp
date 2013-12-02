<div class="packageLoaItemRels form">
<?php echo $form->create('PackageLoaItemRel');?>
	<fieldset>
 		<legend><?php __('Add PackageLoaItemRel');?></legend>
	<?php
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
		<li><?php echo $html->link(__('List PackageLoaItemRels', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
