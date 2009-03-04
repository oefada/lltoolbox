<div class="trackDetails form">
<?php echo $form->create('TrackDetail');?>
	<fieldset>
 		<legend><?php __('Edit TrackDetail');?></legend>
	<?php
		echo $form->input('trackDetailId');
		echo $form->input('trackId');
		echo $form->input('ticketId');
		echo $form->input('iteration');
		echo $form->input('cycle');
		echo $form->input('amountKept');
		echo $form->input('amountRemitted');
		echo $form->input('xyRunningTotal');
		echo $form->input('xyAverage');
		echo $form->input('keepBalDue');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
