<div class="loaItemRatePeriods form">
<?php echo $ajax->form('edit', 'post', array('url' => "/loa_items/{$this->data['LoaItemRatePeriod']['loaItemId']}/loa_item_rate_periods/edit", 'update' => 'MB_content', 'model' => 'LoaItemRatePeriod', 	'complete' => 'closeModalbox()'));?>
	<fieldset>
 		<legend><?php __('Edit LoaItemRatePeriod');?></legend>
	<?php
		echo $form->input('loaItemRatePeriodId', array('type' => 'hidden'));
		echo $form->input('loaItemId', array('type' => 'hidden'));
		echo $form->input('loaItemRatePeriodName');
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('approvedRetailPrice');
		echo $form->input('approved');
		echo $form->input('approvedBy');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<?php
$refreshLoaItemRatePeriods = $ajax->remoteFunction( 
    array(
    'url' => array( 'controller' => 'loa_item_rate_periods', 'action' => 'getRatePeriodsForItem', $this->data['LoaItemRatePeriod']['loaItemId'] ),
    'update' => 'relatedLoaItemRatePeriods_'.$this->data['LoaItemRatePeriod']['loaItemId']) 
);
if (isset($closeModalbox) && $closeModalbox) echo "<script>".$refreshLoaItemRatePeriods."</script><div id='closeModalboxNoReload'></div>";
?>