<?php echo $ajax->form('auto_extend', 'post', array('url' => "/scheduling_instances/auto_extend/schedulingMasterId:{$schedulingMaster['SchedulingMaster']['schedulingMasterId']}", 'update' => 'MB_content', 'complete' => 'closeModalbox()'));?>
	Are you sure you wish to extend this offer for one more iteration?
<?
echo $form->hidden('schedulingMasterId', array('value' => $schedulingMaster['SchedulingMaster']['schedulingMasterId']));
echo $form->end('Continue')
?>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>