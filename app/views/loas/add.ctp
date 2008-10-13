<?php
$this->pageTitle = 'Client LOAs';
$html->addCrumb('Clients', '/clients');
$html->addCrumb($clientName, '/clients/view/'.$this->data['Loa']['clientId']);
$html->addCrumb("LOA's", '/clients/'.$this->data['Loa']['clientId'].'/loas');
$html->addCrumb('New Loa');
?>
<div class="loas form">
<?php echo $form->create('Loa');?>
	<fieldset>
 		<legend><?php __('New Loa');?> for <?=$clientName?></legend>
	<?php
		echo $form->input('clientId', array('type' => 'hidden'));
		echo $form->input('numEmailInclusions');
		echo $form->input('loaValue');
		echo $form->input('remainingBalance');
		echo $form->input('remitStatus');
		echo $form->input('remitPercentage');
		echo $form->input('remitAmount');
		echo $form->input('customerApprovalStatusId');
		echo $form->input('customerApprovalDate');
		echo $form->input('numCommissionFreePackages');
		echo $form->input('useFlatCommission');
		echo $form->input('flatCommissionPercentage');
		echo $form->input('useKeepRemitLogic');
		echo $form->input('upgraded');
		echo $form->input('loaNumberPackages');
		echo $form->input('remainingPackagesToSell');
		echo $form->input('cashPaid');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
