<div class="packages view">
<h2><?php  __('Package');?></h2>
</div>

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

<h3>Package Validity Date Ranges</h3>
<div class="mB mT"><a href="/packages/<?php echo $package['Package']['packageId'];?>/packageValidityPeriods/add">Add New Blackout / Validity</a></div>
<div class="mB">
	<table cellpadding="2" cellspacing="0">
	<tr>
		<th width="100">Package Validity Id</th>
		<th>Start Date</th>
		<th>End Date</th>
		<th>Type</th>
	</tr>
	<?php	
	foreach ($package['PackageValidityPeriod'] as $k=>$v) {
	?>
		<tr>
			<td><a href="/packageValidityPeriods/edit/<?php echo $v['packageValidityPeriodId'];?>"><?php echo $v['packageValidityPeriodId'];?></a></td>
			<td><?php echo $v['startDate'];?></td>
			<td><?php echo $v['endDate'];?></td>
			<td><?php echo $v['isBlackout'] ? 'BLACKOUT' : 'VALIDITY';?></td>
		</tr>
	<?php
	}
	?>
	</table>
</div>

<h3>Package LOA Items</h3>
<div class="mB mT"><a href="/packages/<?php echo $package['Package']['packageId'];?>/packageLoaItemRels/add">Add New LOA Item to Package</a></div>
<div class="mB">
	<table cellpadding="2" cellspacing="0">
	<tr>
		<th width="100">Package Item Id</th>
		<th>Item Id</th>
		<th>Name</th>
		<th>Group Id</th>
		<th>Price Override</th>
		<th>Quanitiy</th>
		<th>No charge</th>
	</tr>
	<?php	
	foreach ($package['PackageLoaItemRel'] as $k=>$v) {
	?>
		<tr>
			<td><a href="/packageLoaItemRels/edit/<?php echo $v['packageLoaItemRelId'];?>"><?php echo $v['packageLoaItemRelId'];?></a></td>
			<td><?php echo $v['loaItemId'];?></td>
			<td><?php echo $v['LoaItem']['itemName'];?></td>
			<td><?php echo $v['loaItemGroupId'];?></td>
			<td><?php echo $v['priceOverride'];?></td>
			<td><?php echo $v['quantity'];?></td>
			<td><?php echo $v['noCharge'];?></td>
		</tr>
	<?php
	}
	?>
	</table>
</div>

<?= $this->renderElement('../packages/package_rate_periods', array('packageRatePeriods' => $packageRatePeriods, 'package' => $package)) ?>

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