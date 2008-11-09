<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:');
?>
<div class="packages form">
<?php echo $form->create('Package', array('url' => "/clients/{$clientId}/packages/edit/{$this->data['Package']['packageId']}", 'id'=>'PackageAddForm'));?>
<?php echo $this->renderElement('../packages/_add_step_1'); ?>
<?php echo $this->renderElement('../packages/_add_step_2'); ?>
<?php echo $this->renderElement('../packages/_add_step_3'); ?>
<?php echo $this->renderElement('../packages/_add_step_4'); ?>
<?php echo $this->renderElement('../packages/_add_step_5'); ?>
<?php echo $this->renderElement('../packages/_add_step_6'); ?>
<?php echo $form->input('Package.packageId'); ?>
<?php 
	foreach($this->data['ClientLoaPackageRel'] as $k => $v):
		echo $form->input('ClientLoaPackageRel.'.$k.'.clientLoaPackageRelId');
	endforeach;
?>
<?php echo $form->end('Submit');?>

<h3>Package Formats</h3>
<div class="mB mT"><a href="/Packages/editFormats/<?php echo $package['Package']['packageId'];?>">Edit Default Values</a></div>
<div class="mB">
	<table>
	<tr>
		<th>Format Type</th>
		<th>Default Values</th>
		<th>Options</th>
	</tr>
	<?php
	foreach ($package['Format'] as $k => $v) {
		echo "<tr>";
		echo "<td>$v[formatName]</td>";
		echo "<td>*</td>";
		echo "<td><a href='#'>Schedule this Offer Type</a></td>";
		echo "</tr>";
	}
	?>
	</table>
</div>

<h3>Package Rate Periods</h3>
<div class="mB mT">
	<table cellpadding="2" cellspacing="0">
	
	<?php
	echo '<tr>';
	echo '<th>Range</th>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<th>' . $v['prp']['startDate'] . '<br />to<br />' . $v['prp']['endDate'] . "</th>\n";
	}
	echo '</tr>';
	?>	

	<?php
	foreach ($package['PackageLoaItemRel'] as $a => $b) {
		echo '<tr>';
		echo '<td>' . $b['LoaItem']['itemName'] . '</td>';
		foreach ($b['PackageRatePeriodItemRel'] as $ratePeriodItem) {
			echo '<td>$' . $ratePeriodItem['ratePeriodPrice'] . '</td>';
		}
		echo '</tr>';
	}
	?>

	<?php
	echo '<tr>';
	echo '<td>Overall Price</td>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<td><strong>$' . $v['prp']['approvedRetailPrice'] . '</strong></td>';
	}
	echo '</tr>';
	?>
	
	</table>
</div>

<h3>Package Promos</h3>
<div class="mB mT"><a href="/packages/<?php echo $package['Package']['packageId'];?>/packagePromos/add">Add New Package Promo</a></div>
<div class="mB">
	<table>
	<tr>
		<th>Package Promo Id</th>
		<th>Description</th>
		<th>Promo Code</th>
	</tr>
	<?php
	foreach ($package['PackagePromo'] as $k => $v) {
		echo '<tr>';
		echo '<td><a href="/packagePromos/edit/'. $v['packagePromoId'] . '">' . $v['packagePromoId'] . '</a></td>';
		echo '<td>' . $v['description'] . '</td>';
		echo '<td>' . $v['promoCode'] . '</td>';
		echo '</tr>';
	}
	?>
	</table>
</div>
</div>