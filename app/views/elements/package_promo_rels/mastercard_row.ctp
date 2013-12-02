<?php
global $totalCollected;
$rowspan = ($numTickets > 0) ? $numTickets : 1;

?>
<tr<?php if($k %2) echo ' class="altrow"' ?>>
  <td rowspan="<?=$rowspan?>" style="text-align: center">
<input type="checkbox" name="data[<?=$checkboxAction?>][]" value="<?=$package['PackagePromoRel']['packagePromoRelId']?>" />	
</td>
  <td rowspan="<?=$rowspan?>"><?=$package['Client']['clientId']?></td>
  <td rowspan="<?=$rowspan?>"><?=$html->link($package['Package']['packageId'], '/scheduling/index/clientId:'.$package['Client']['clientId'])?></td>
  <td rowspan="<?=$rowspan?>">
	<? if(!empty($package['Theme'])): ?>
	<?=implode(', ', $package['Theme'])?>
	<br />
	<? endif; ?>
	<?php
	echo $html->link('Benefit Copy',
					'/package_promo_rels/ajax_edit/'.$package['PackagePromoRel']['packagePromoRelId'],
					array(
						'title' => 'Edit Benefit Copy',
						'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
						'complete' => 'closeModalbox()'
						),
					null,
					false
					);
	?></td>
  <td rowspan="<?=$rowspan?>"><?=$html->link($package['Client']['name'], '/clients/edit/'.$package['Client']['clientId'])?></td>
  <td rowspan="<?=$rowspan?>"><?=($package['OfferLive']['endDate']) ? date('M d, Y', strtotime($package['OfferLive']['endDate'])) : '';?></td>
  <td><a href="/tickets?query=<?=@$package['Ticket'][0]['Ticket']['ticketId']?>"><?=@$package['Ticket'][0]['Ticket']['ticketId']?></a></td>
  <? $collected = @$package['Ticket'][0]['Ticket']['billingPrice']; ?>
  <td><table class="noBorder" style="width: 100%; margin: 0; padding: 0"><tr><td nowrap><?=@$offerTypes[$package['Ticket'][0]['Ticket']['offerTypeId']]?></td><td align="right">$<?=@$package['Ticket'][0]['Ticket']['billingPrice']?></td></tr></table></td>
  <td><? if (@$package['Ticket'][0]['Ticket']['ticketId']) echo "$150"; ?></td>
  <td rowspan="<?=$rowspan?>" style="background-color: #ebebeb"><?=count($package['Ticket'])?></td>
  <td rowspan="<?=$rowspan?>" style="background-color: #ebebeb">$<?=count($package['Ticket'])*150?></td>
	<?
	$rowCollected = 0;
	foreach($package['Ticket'] as $ticket ){ //for multiple tickets
		$rowCollected += $ticket['Ticket']['billingPrice'];
	}
	?>
  <td rowspan="<?=$rowspan?>" style="background-color: #ebebeb">$<?=$rowCollected?></td>
</tr>
<?php
array_shift($package['Ticket']); // remove the first one since we took care of it already
foreach( $package['Ticket'] as $k2 => $ticket ): //for multiple tickets
?>
<tr<?php if($k %2) echo ' class="altrow"' ?>>
  <td><a href="/tickets?query=<?=$package['Ticket']['ticketId']?>"><?=$ticket['Ticket']['ticketId']?></a></td>
  <td><table class="noBorder" style="width: 100%; margin: 0; padding: 0"><tr><td nowrap><?=$offerTypes[$ticket['Ticket']['offerTypeId']]?></td><td align="right">$<?=@$ticket['Ticket']['billingPrice']?></td></tr></table></td>
  <td>$150</td>
  </tr>
 <?php endforeach; ?>
<?php
$prototip->tooltip("benefitCopy_{$package['PackagePromoRel']['packagePromoRelId']}", nl2br($package['PackagePromoRel']['benefitCopy']));
?>