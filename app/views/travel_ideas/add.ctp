<div class="travelIdeas form">
<?php echo $form->create('TravelIdea', array('url' => 'add/' . $this->data['TravelIdea']['landingPageId']));?>
	<fieldset>
 		<legend><?php __('Add Travel Idea');?></legend>
	<?php
		echo $form->input('landingPageId');
		echo $form->input('travelIdeaHeader');
		echo $form->input('travelIdeaBlurb');
		echo $form->input('travelIdeaLinkText');
		echo $form->input('travelIdeaUrl');
		echo $form->input('tripAdvisorAward');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
