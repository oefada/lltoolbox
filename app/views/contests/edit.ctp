<div class="contests form">
<?php echo $form->create('Contest');?>
	<fieldset>
 		<legend><?php __('Edit Contest');?></legend>
	<?php
		echo $form->input('contestId');
		echo $form->input('contestName');
		echo $form->input('descriptionText');
		echo $form->input('clientIds', array('label' => 'Associated ClientIds<br/>(comma delimited)', 'value' => $clientIds));
		//echo $form->input('url');
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('displayText', array('label' => 'Email Message'));
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
