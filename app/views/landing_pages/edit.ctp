<div class="landingPages form">
<?php echo $form->create('LandingPage');?>
	<fieldset>
 		<legend><?php __('Edit LandingPage');?></legend>
		<div class="controlset4">
		<?
		echo $form->input('sites', array('multiple' => 'checkbox'));
		?>
		</div>
	<?php
		echo $form->input('landingPageId');
		echo $form->input('landingPageName');
		echo $form->input('landingPageTypeId');
		echo $form->input('referenceId');
		echo '<div class="controlset">' . $form->input('isSponsored') . '</div>';
		echo $form->input('pageTitle');
		echo $form->input('metaDescription');
		echo $form->input('textHeader');
		echo $form->input('textBody');
		echo $form->input('communityUrl');
		echo '<div class="controlset">' . $form->input('mainHomepageStyle') . '</div>';
		echo '<div class="controlset">' . $form->input('inactive') . '</div>';
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
