<div class="landingPages form">
<?php echo $form->create('LandingPage');?>
	<fieldset>
 		<legend><?php __('Add LandingPage');?></legend>
		<div class="controlset4">
            <?php echo $form->input('siteId', array('label' => 'Site')); ?>
		</div>
	<?php
		echo $form->input('landingPageName');
		echo $form->input('landingPageTypeId');
		echo $form->input('referenceId');
		echo $form->input('pageTitle');
		echo $form->input('metaDescription');
		echo $form->input('textHeader');
		echo $form->input('textBody');
		echo $form->input('communityUrl');
		echo $form->input('mainHomepageStyle');
		echo $form->input('inactive');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
