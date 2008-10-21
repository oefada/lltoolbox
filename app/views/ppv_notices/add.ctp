<div class="ppvNotices form">
<?php echo $form->create('PpvNotice');?>
	<fieldset>
 		<legend><?php __('Add PpvNotice');?></legend>
	<?php
		echo $form->input('ppvNoticeTypeId');
		echo $form->input('ticketId');
		echo $form->input('to');
		echo $form->input('from');
		echo $form->input('cc');
		echo $form->input('body');
		echo $form->input('dateSent');
		echo $form->input('subject');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List PpvNotices', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Tickets', true), array('controller'=> 'tickets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket', true), array('controller'=> 'tickets', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ppv Notice Types', true), array('controller'=> 'ppv_notice_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ppv Notice Type', true), array('controller'=> 'ppv_notice_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
