<tr<?php if($k %2) echo ' class="altrow"' ?>>
  <td rowspan="1" style="text-align: center">
<input type="checkbox" name="data[<?=$checkboxAction?>][]" value="<?=$package['PackagePromoRel']['packagePromoRelId']?>" />	
</td>
  <td rowspan="1"><?=$package['Client']['clientId']?></td>
  <td rowspan="1"><?=$html->link($package['Package']['packageId'], '/scheduling/index/clientId:'.$package['Client']['clientId'])?></td>
  <td rowspan="1">
	<? if(!empty($package['Theme'])): ?>
	<?=implode(', ', $package['Theme'])?>
	<br />
	<? endif; ?>
	<a href="#" id="benefitCopy_<?=$package['PackagePromoRel']['packagePromoRelId']?>" onclick="return false;">Benefit Copy</a></td>
  <td rowspan="1"><?=$html->link($package['Client']['name'], '/clients/edit/'.$package['Client']['clientId'])?></td>
  <td rowspan="1"><?=$package['OfferLive']['endDate']?></td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td rowspan="1" style="background-color: #ebebeb">&nbsp;</td>
  <td rowspan="1" style="background-color: #ebebeb">&nbsp;</td>
  <td rowspan="1" style="background-color: #ebebeb">&nbsp;</td>
</tr>
<?php if(0): //for multiple tickets?>
<tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  </tr>
 <?php endif; ?>
<?php
$prototip->tooltip("benefitCopy_{$package['PackagePromoRel']['packagePromoRelId']}", nl2br($package['PackagePromoRel']['benefitCopy']));
?>