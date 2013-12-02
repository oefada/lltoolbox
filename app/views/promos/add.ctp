<div class="promos form">
<?php echo $form->create('Promo');?>
	<fieldset>
 		<legend><?php __('Add Promo');?></legend>
	<?php
		echo $form->input('promoName');
		echo $form->input('percentOff');
		echo $form->input('amountOff');
		echo $form->input('minPurchaseAmount');
		echo '<div class="controlset">' . $form->input('oneUsagePerCode') . '</div>';
		echo '<div class="controlset">' . $form->input('oneUsagePerUser') . '</div>';
		echo '<div class="controlset">' . $form->input('newBuyersOnly') . '</div>';
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('siteId', array('options'=> array('0'=>'All', '1'=>'LuxuryLink', '2'=>'Family')));
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Promos', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Promo Codes', true), array('controller'=> 'promo_codes', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo Code', true), array('controller'=> 'promo_codes', 'action'=>'add')); ?> </li>
	</ul>
</div>
