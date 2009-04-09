<div class="travelIdeaItems form">
<?php echo $form->create('TravelIdeaItem', array('url' => "add/$travelIdeaId/$landingPageId"));?>
	<fieldset>
 		<legend><?php __('Add Travel Idea Item');?></legend>
	<?php
		echo $form->input('travelIdeaItemTypeId');
		echo $form->input('travelIdeaId');
		echo $form->input('travelIdeaItemName');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
