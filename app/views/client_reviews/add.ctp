<div class="clientReviews form">
<?php echo $form->create('ClientReview');?>
	<fieldset>
 		<legend><?php __('Add ClientReview');?></legend>
	<?php
		echo $form->input('clientId');
		echo $form->input('authorUserId');
		echo $form->input('reviewTitle');
		echo $form->input('reviewBody');
		echo $form->input('datetime');
		echo $form->input('status', array('value' => 'approved'));
		echo '<div class="controlset">'.$form->input('inactive')."</div>";
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List ClientReviews', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Users', true), array('controller'=> 'users', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller'=> 'users', 'action'=>'add')); ?> </li>
	</ul>
</div>
