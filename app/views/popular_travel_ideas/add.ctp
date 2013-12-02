<div class="popularTravelIdeas form">
<?php echo $form->create('PopularTravelIdea');?>
	<fieldset>
 		<legend><?php __('Add PopularTravelIdea');?></legend>
	<?php
		echo $form->input('referenceId');
		echo $form->input('popularTravelIdeaName');
		echo $form->input('linkToMultipleStyles');
		echo $form->input('keywords');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List PopularTravelIdeas', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Styles', true), array('controller'=> 'styles', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Style', true), array('controller'=> 'styles', 'action'=>'add')); ?> </li>
	</ul>
</div>
