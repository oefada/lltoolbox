<div class="travelIdeaItems form">
<?php echo $form->create('TravelIdeaItem', array('url' => "edit/$tiId/$landingPageId"));?>
	<fieldset>
 		<legend><?php __('Edit Travel Idea Item');?></legend>
		<div class="controlset4">
		<?
		echo $form->input('sites', array('multiple' => 'checkbox'));
		?>
		</div>
	<?php
		echo $form->input('travelIdeaItemId');
		echo $form->input('travelIdeaItemTypeId');
		echo $form->input('travelIdeaId');
		echo $form->input('travelIdeaItemName');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
