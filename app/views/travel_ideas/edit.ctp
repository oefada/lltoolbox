<div class="travelIdeas form">
<?php echo $form->create('TravelIdea', array('url' => 'edit/' . $this->data['TravelIdea']['travelIdeaId']));?>
	<fieldset>
 		<legend><?php __('Edit Travel Idea');?></legend>
		<div class="controlset4">
		<?
		echo $form->input('sites', array('multiple' => 'checkbox'));
		?>
		</div>
	<?php
		echo $form->input('travelIdeaId');
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
