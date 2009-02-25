<?php echo $form->create('OfferLive');?>
<fieldset>
		<label>Offer ID</label><?=$this->data['OfferLive']['offerId']?>
	<?php
		echo $form->input('offerId');
		echo $form->input('offerName');

		//only Geeks group can alter the end date of a live offer
		if (in_array('Geeks', $user['LdapUser']['groups'])) {
			echo $form->input('endDate');	
		}
		
		echo $form->input('buyNowPrice');
		echo $form->input('termsAndConditions');
		echo $form->input('validityDisclaimer');
		echo $form->input('additionalDescription');
		echo $form->input('validityStart');
		echo $form->input('validityEnd');
	?>
</fieldset>
<?php echo $form->end('Commit Changes');?>