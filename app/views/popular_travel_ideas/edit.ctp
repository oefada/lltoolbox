<div class="popularTravelIdeas form">
<?php echo $form->create('PopularTravelIdea');?>
	<fieldset>
 		<legend><?php __('Edit PopularTravelIdea');?></legend>
	<?php
		echo $form->input('popularTravelIdeaId');
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
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('PopularTravelIdea.popularTravelIdeaId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('PopularTravelIdea.popularTravelIdeaId'))); ?></li>
		<li><?php echo $html->link(__('List PopularTravelIdeas', true), array('action'=>'index'));?></li>
	</ul>
</div>
