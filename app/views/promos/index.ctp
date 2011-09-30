<div class="promos index">
<h2><?php __('List Promos');?></h2>
	<div id="ticket-search-box">
		<form action="/promos/index" method="post" id="promos-search-form" name="promos-search-form">
				
		<table cellpadding="0" cellspacing="0" style="border:1px solid silver;">
		<tr>
		<td style="width:680px; padding-right:20px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0"> 
					<tr>
					    <td class="leftFilterLabel">Name</td>
					    <td><?= $form->input('s_name', array('label'=>false, 'div'=>false, 'style'=>'width:200px')); ?></td>
					</tr>
					<tr>
					    <td class="leftFilterLabel">Site</td>
					    <td><?= $form->input('s_site_id', array('label'=>false, 'div'=>false, 'options'=> array('0'=>'--', '1'=>'LuxuryLink', '2'=>'Family'))); ?></td>
					</tr>
					<tr>
					    <td class="leftFilterLabel">Destination</td>
					    <td><?= $form->input('s_destination_id', array('label'=>false, 'div'=>false, 'options'=> $destinations, 'empty'=> '--')); ?></td>
					</tr>
					<tr>
					    <td class="leftFilterLabel">Theme</td>
					    <td><?= $form->input('s_theme_id', array('label'=>false, 'div'=>false, 'options'=> $themes, 'empty'=> '--')); ?></td>
					</tr>
					<tr>
					    <td class="leftFilterLabel">Property Type</td>
					    <td><?= $form->input('s_client_type_id', array('label'=>false, 'div'=>false, 'options'=> $clientTypes, 'empty'=> '--')); ?></td>
					</tr>
					<tr>
					    <td class="leftFilterLabel">Valid Range</td>
					    <td><?= $datePicker->picker('s_start_date', array('label'=>false, 'div'=>false, 'style'=>'width:90px')); ?>
						&nbsp;&nbsp;&nbsp;
						<?= $datePicker->picker('s_end_date', array('label'=>false, 'div'=>false, 'style'=>'width:90px')); ?>
					    </td>
					</tr>
					</table>
				</td>
				<td>
					<table cellpadding="0" cellspacing="0"> 
					<tr>
					    <td class="leftFilterLabel">Category</td>
					    <td><?= $form->input('s_categories', array('type'=>'select', 'multiple'=>true, 'label'=>false, 'div'=>false, 'style'=>'height:132px','options'=> $promoCategoryTypeIds, 'empty'=> 'All')); ?></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		
		</td>
		<td align="left" style="width:200px; border-left:1px solid silver; padding-right:20px;">
			<table cellspacing="0" cellpadding="0">
			<tr>
				<td class="rightFilterLabel">Client Id</td>
				<td><?= $form->input('s_client_id', array('label'=>false, 'div'=>false, 'style'=>'width:100px')); ?></td>
			</tr>
			<tr>
				<td class="rightFilterLabel">Promo Code</td>
				<td><?= $form->input('s_promo_code', array('label'=>false, 'div'=>false, 'style'=>'width:100px')); ?></td>
			</tr>
			</table>

		</td>
		<td style="width:30%; border-left:1px solid silver; padding-left:10px; padding-top:10px;">
		<input type="submit" name="s_submit" value="Search" />
		<? if (false) { ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" name="s_submit_csv" value="Export to CSV" style="width:120px;"/>
		<? } ?>
		</td>
		</tr>
		</table>
		
		</form>
	</div>

<? if (isset($paginator)) { echo $this->renderElement('ajax_paginator'); } ?>
<table cellpadding="0" cellspacing="0">

<? if ($promos) { ?>
<tr>
	<th>Internal Promotion Name</th>
	<th>Category</th>
	<th>Promotion Code</th>
	<th>% Off</th>
	<th>$ Off</th>
	<th>Min $</th>
   	<th>Start Date</th>
   	<th>End Date</th>
   	<th>Site</th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<? } ?>
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
		<td><a href="/promos/edit/<?= $promo['Promo']['promoId']; ?>"><?php echo $promo['Promo']['promoName']; ?></a></td>
		<td><?php echo $promo['PromoCategoryType']['promoCategoryTypeName']; ?></td>
		<td>
		<? if ($promo[0]['numPromoCode'] == 1) {  
		       echo $promo_code;
		   } else {
		       echo $html->link(__($promo[0]['numPromoCode'] . ' unique codes', true), array('controller'=>'promo_code_rels', 'action'=>'index', $promo['Promo']['promoId']));
		   }
		?>
		</td>
		<td align="right"><?php echo $promo['Promo']['percentOff']; ?></td>
		<td align="right"><?php echo $promo['Promo']['amountOff']; ?></td>
		<td align="right"><?php echo $promo['Promo']['minPurchaseAmount']; ?></td>
		<td><?php echo $promo['Promo']['startDate']; ?></td>
		<td><?php echo $promo['Promo']['endDate']; ?></td>
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
            <?php echo $html->link(__('Add Codes', true), array('controller'=>'promo_codes', 'action'=>'add', $promo['Promo']['promoId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>