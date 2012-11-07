<?php
$ticket = $this->data;
$this->pageTitle = 'Details for Ticket #' . $this->data['Ticket']['ticketId'];
?>
<div class="tickets form">
<?php echo $form->create(null, array('url' => '/tickets/updateDetails')); ?>
	<fieldset>
	<?php
		echo $form->input('ticketId', array('type' => 'hidden'));
		echo $form->input('billingPrice');
		echo $form->input('numNights');
		echo $form->input('requestArrival');
		echo $form->input('requestDeparture');		
	?>
	
	<div class="input textarea">
		<label for="extraNotes">Add Note</label>
		<textarea id="extraNotes" rows="6" cols="80" name="data[extraNotes]"><?= $this->data['extraNotes']; ?></textarea>
	</div>	
	
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
