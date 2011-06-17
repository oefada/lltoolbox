<div class="clientReviews form">
<?php echo $form->create('ClientReview');?>
	<fieldset>
 		<legend><?php __('Add ClientReview');?></legend>

		<div class="controlset4">
			<div class="input select">
				<label for="ClientReviewSites">Sites</label>
				<div class="checkbox">
					<input type="checkbox" name="data[ClientReview][sites][]" value="luxurylink" id="ClientReviewSitesLuxuryLink">
					<label for="ClientReviewSitesLuxuryLink">Luxury Link</label>
				</div>
				<div class="checkbox">
					<input type="checkbox" name="data[ClientReview][sites][]" value="family" id="ClientReviewSitesFamily">
					<label for="ClientReviewSitesFamily">Family Getaway</label>
				</div>
			</div>
		</div>

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
