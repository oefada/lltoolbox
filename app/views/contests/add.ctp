<div class="contests form">
<?php echo $form->create('Contest');?>
	<fieldset>
 		<legend><?php __('Add Contest');?></legend>
	<?php
		echo $form->input('contestName');
		echo $form->input('descriptionText');
		echo $form->input('clientIds', array('label' => 'Associated ClientIds<br/>(comma delimited)'));
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('displayText', array('label' => 'Email Message', 'value' => '<br><br>Dear World Traveler:<br><br>Thank you for entering the Luxury Link drawing for a trip for two. Good luck and keep checking the Luxury Link web site for more contests, auctions and news!<br><br>Check out our <a href="http://www.luxurylink.com/auctions/auc_mystery.php"><b>$1 Starting Bid Auctions</b></a> - there is NO RESERVE and someone always wins at an amazing price.<br><br>Cheers,<br>Luxury Link<br><br><a href="http://www.luxurylink.com"><b>www.LuxuryLink.com</b></a>'));
		echo $form->input('html', array('label' => 'Homepage Copy'));
		echo '<div class="controlset">'.$form->input('inactive')."</div>";
		//echo $form->input('legalText');
		//echo $form->input('title');
		//echo $form->input('titleImage');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Contests', true), array('action'=>'index'));?></li>
	</ul>
</div>
