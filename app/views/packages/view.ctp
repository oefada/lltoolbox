<div class="packages view">
<h2><?php  __('Package');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['packageId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageStatusId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['PackageStatus']['packageStatusName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CurrencyId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Currency']['currencyName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['packageName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Subtitle'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['subtitle']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CurrencyAsOfDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['currencyAsOfDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumSold'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['numSold']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumConcurrentOffers'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['numConcurrentOffers']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SuppressRetailOnDisplay'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['suppressRetailOnDisplay']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('StartDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['startDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('EndDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['endDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MaxOffersToSell'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['maxOffersToSell']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateClientApproved'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['dateClientApproved']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CopiedFromPackageId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['copiedFromPackageId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Restrictions'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['restrictions']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ValidityStartDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['validityStartDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ValidityEndDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['validityEndDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ApprovedRetailPrice'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['approvedRetailPrice']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumNights'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['numNights']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumGuests'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $package['Package']['numGuests']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Formats'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php
			$zz = 0;
			foreach ($package['Format'] as $k => $v) {
				$zz++;
				echo "($zz) " . $v['formatName'] . '&nbsp;&nbsp;&nbsp;';
			}
			?>
			&nbsp;
		</dd>
	</dl>
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
		echo "<td>Defaults are Set</td>";
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
			<td><?php echo $v['validityFlag'] ? 'VALID' : 'BLACKOUT';?></td>
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

<h3>Package Rate Periods</h3>
<div class="mB mT">
	<table cellpadding="2" cellspacing="0">
	
	<?php
	echo '<tr>';
	echo '<th>Range</th>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<th>' . $v['PackageRatePeriod']['startDate'] . '<span style="margin-left:15px;margin-right:15px;">to</span>' . $v['PackageRatePeriod']['endDate'] . "</th>\n";
	}
	echo '</tr>';
	?>	

	<?php
	foreach ($package['PackageLoaItemRel'] as $a => $b) {
		echo '<tr>';
		echo '<td>' . $b['LoaItem']['itemName'] . '</td>';
		foreach ($b['PackageRatePeriodItemRel'] as $ratePeriodItem) {
			echo '<td>' . $ratePeriodItem['ratePeriodPrice'] . '</td>';
		}
		echo '</tr>';
	}
	?>

	<?php
	echo '<tr>';
	echo '<td>Overall Price</td>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<td><strong>$' . $v['PackageRatePeriod']['approvedRetailPrice'] . '</strong></td>';
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

<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Package', true), array('action'=>'edit', $package['Package']['packageId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Package', true), array('action'=>'delete', $package['Package']['packageId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $package['Package']['packageId'])); ?> </li>
		<li><?php echo $html->link(__('List Packages', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Package', true), array('action'=>'add')); ?> </li>
	</ul>
</div>
