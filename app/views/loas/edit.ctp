<?php
$this->pageTitle = 'Edit Client Loa';
$html->addCrumb('Clients', '/clients');
$html->addCrumb($text->truncate($this->data['Client']['name'], 15), '/clients/view/'.$this->data['Client']['clientId']);
$html->addCrumb('LOA\'s', '/clients/'.$this->data['Client']['clientId'].'/loas');
$html->addCrumb('LOA #'.$this->data['Loa']['loaId'], '/loas/view/'.$this->data['Loa']['loaId']);
$html->addCrumb('Edit');
?>
<?=$layout->blockStart('header');?>
<?= $html->link('<span><b class="icon"></b>Delete LOA</span>', array('action'=>'delete', $form->value('Loa.loaId')), array('class' => 'button del'), sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Loa.loaId')), false); ?>
<?=$layout->blockEnd();?>

<div class="loas form">
<?php echo $form->create('Loa');?>
	<fieldset>
 		<legend><?php __('Edit Loa');?></legend>
		<fieldset class="collapsible">
			<legend>Client: <?= $this->data['Client']['name'];?></legend>
			<?php echo $ajax->link(
				'Show Details',
					array( 'controller' => 'clients', 'action' => 'view', $this->data['Loa']['clientId'] ),
					array( 'update' => 'clientDetails' )
					);
			?>
			<?php echo $ajax->link('Hide Details')?>
			<div id="clientDetails"></div>
		</fieldset>
	<?php
		echo $form->input('loaId');
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
