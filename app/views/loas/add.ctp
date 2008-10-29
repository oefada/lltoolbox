<?php
$this->pageTitle = $clientName.$html2->c($this->data['Loa']['clientId'], 'Client Id:');
$this->set('clientId', $this->data['Loa']['clientId']);
?>
<h2 class="title">New LOA</h2>
<div class="loas form">
<?php echo $form->create('Loa');?>
	<fieldset>
	<?php
		echo $form->input('clientId', array('type' => 'hidden'));
		echo $form->input('numEmailInclusions');
		echo $form->input('loaValue');
		echo $form->input('customerApprovalStatusId');
		echo '<div class="controlset">'.$form->input('upgraded')."</div>";
		echo $form->input('loaNumberPackages');
		echo $form->input('startDate');
		echo $form->input('endDate');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>