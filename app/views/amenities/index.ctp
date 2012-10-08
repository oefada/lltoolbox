<?php
$this->pageTitle = 'Amenities';
?>

<div id="client-index">
	<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<div class="amenities index">
	<?php if (isset($query) && !empty($query)): ?>
		<div style="clear: both">
		<strong>Search Criteria:</strong> <?php echo $query; ?> 
		<?php if($inactive == 1): ?>
			<a href="/amenities/search?query=<?=$query?>">(hide inactive)</a>
		<?php else: ?>
			<a href="/amenities/search?query=<?=$query?>&inactive=1">(show inactive)</a>
		<?php endif; ?>
		</div>
	<?php endif ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('amenityId');?></th>
	<th><?php echo $paginator->sort('amenityName');?></th>
    <th>Amenity Parent</th>
    <th><?php echo $paginator->sort('amenityTypeName');?></th>
	<th><?php echo $paginator->sort('inactive');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($amenities as $amenity):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td><?php echo $amenity['Amenity']['amenityId']?></td>
		<td>
			<?php echo $amenity['Amenity']['amenityName']; ?>
		</td>
		<td>
			<?php echo $amenity['Amenity']['parentAmenity']; ?>
		</td>
		<td>
			<?php echo $amenity['amenityType']['amenityTypeName']; ?>
		</td>
        <td>
			<?php echo $amenity['Amenity']['inactive']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('action'=>'edit', $amenity['Amenity']['amenityId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<?php echo $this->renderElement('ajax_paginator'); ?>
</div>
