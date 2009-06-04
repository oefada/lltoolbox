<div class="promos form">
<?php echo $form->create('Promo');?>
	<fieldset>
 		<legend><?php __('Edit Promo');?></legend>
	<?php
/*		echo $form->input('promoId');
		echo $form->input('promoName');
		echo $form->input('percentOff');
		echo $form->input('amountOff');
		echo $form->input('minPurchaseAmount');
		echo $form->input('maxNumUsage');*/
		echo $form->input('promoName');
		echo $form->input('percentOff');
		echo $form->input('amountOff');
		echo $form->input('minPurchaseAmount');
		echo '<div class="controlset">' . $form->input('oneUsagePerCode') . '</div>';
		echo '<div class="controlset">' . $form->input('oneUsagePerUser') . '</div>';
		echo '<div class="controlset">' . $form->input('newBuyersOnly') . '</div>';
		echo $form->input('startDate');
		echo $form->input('endDate');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Promos', true), array('action'=>'index'));?></li>
	</ul>
</div>
