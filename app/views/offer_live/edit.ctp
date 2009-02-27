<?php echo $form->create('OfferLive');?>
<fieldset>
		<label>Offer ID</label><?=$this->data['OfferLive']['offerId']?>
	<?php
		echo $form->input('offerId');
		echo $form->input('offerName');
		echo $form->input('endDate');
		echo $form->input('buyNowPrice');
		echo $form->input('termsAndConditions');
		echo $form->input('validityDisclaimer');
		echo $form->input('additionalDescription');
		echo $form->input('shortBlurb');
		echo $form->input('validityStart');
		echo $form->input('validityEnd');
	?>
</fieldset>
<?php echo $form->end('Commit Changes');?>