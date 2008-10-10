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
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Loa.loaId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Loa.loaId'))); ?></li>
		<li><?php echo $html->link(__('List Loas', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Loa Customer Approval Statuses', true), array('controller'=> 'loa_customer_approval_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Customer Approval Status', true), array('controller'=> 'loa_customer_approval_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Loa Package Rels', true), array('controller'=> 'client_loa_package_rels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Loa Package Rel', true), array('controller'=> 'client_loa_package_rels', 'action'=>'add')); ?> </li>
	</ul>
</div>
