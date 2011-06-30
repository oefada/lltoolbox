<div class="promos index">
<h2><?php __('Promos');?></h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th>promoId</th>
	<th>Internal Promotion Name</th>
	<th>Promotion Code</th>
	<th>Percent Off</th>
	<th>Dollar Off</th>
	<th>Minimum <br/>Purchase Amount</th>
   	<th>Start Date</th>
   	<th>End Date</th>
   	<th># of Codes</th>
   	<th>Site</th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($promos as $promo):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
	$promo_code = ($promo[0]['numPromoCode'] > 1) ? '<b>unique</b>' : $promo['PromoCode']['promoCode'];
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $promo['Promo']['promoId']; ?>
		</td>
		<td>
			<?php echo $promo['Promo']['promoName']; ?>
		</td>
		<td>
			<?php echo $promo_code; ?>
		</td>
		<td>
			<?php echo $promo['Promo']['percentOff']; ?>
		</td>
		<td>
			<?php echo $promo['Promo']['amountOff']; ?>
		</td>
		<td>
			<?php echo $promo['Promo']['minPurchaseAmount']; ?>
		</td>
		<td>
			<?php echo $promo['Promo']['startDate']; ?>
		</td>
		<td>
			<?php echo $promo['Promo']['endDate']; ?>
		</td>
		<td>
			<?php echo $promo[0]['numPromoCode']; ?>
		</td>
		<td>
			<?php $promoSite = intval($promo['Promo']['siteId']);
			      if ($promoSite == 1) {
			          echo 'LL';
			      } elseif ($promoSite == 2) {
			          echo 'FG';
			      } elseif ($promoSite == 0) {
			          echo 'All';
			      } ?>
		</td>
		<td class="actions">
	        <?php echo $html->link(__('Report', true), array('action'=>'report', $promo['Promo']['promoId'])); ?>
            <?php echo $html->link(__('View Codes', true), array('controller'=>'promo_code_rels', 'action'=>'index', $promo['Promo']['promoId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>

<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Promo', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Promo Codes', true), array('controller'=> 'promo_codes', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo Code', true), array('controller'=> 'promo_codes', 'action'=>'add')); ?> </li>
	</ul>
</div>
