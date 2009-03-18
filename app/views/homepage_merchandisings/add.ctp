<div class="homepageMerchandisings form">
<?php echo $form->create('HomepageMerchandising');?>
	<fieldset>
 		<legend><?php __('Add HomepageMerchandising');?></legend>
	<?php
		echo $form->input('homepageMerchandisingId');
		echo $form->input('homepageMerchandisingTypeId');
		echo $form->input('packageId');
		echo $form->input('title');
		echo $form->input('linkText');
		echo $form->input('linkUrl');
		echo $form->input('html');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List HomepageMerchandisings', true), array('action'=>'index'));?></li>
	</ul>
</div>
