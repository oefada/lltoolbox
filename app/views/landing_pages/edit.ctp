<div class="landingPages form">
<?php echo $form->create('LandingPage');?>
	<fieldset>
 		<legend><?php __('Edit LandingPage');?></legend>
	<?php
		echo $form->input('landingPageId');
		echo $form->input('landingPageName', array('disabled'=>'disabled'));
		echo $form->input('landingPageTypeId', array('disabled'=>'disabled'));
		echo $form->input('referenceId', array('disabled'=>'disabled'));
		echo $form->input('isSponsored');
		echo $form->input('pageTitle');
		echo $form->input('metaDescription');
		echo $form->input('textHeader');
		echo $form->input('textBody');
		echo $form->input('communityUrl');
		echo $form->input('mainHomepageStyle');
		echo $form->input('tripAdvisorAward');
		echo $form->input('inactive');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
