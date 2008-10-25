<ul>
<?php foreach($amenities as $amenity): ?>
<li id="<?=$amenity['Amenity']['amenityId']?>"><?php echo $amenity['Amenity']['amenityName']; ?></li>
<?php endforeach; ?>
</ul>
