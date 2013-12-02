<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$text->excerpt($row['Amenity']['amenityName'], $query, 20),
											$query
										)."<br />".$html2->c($row['Amenity']['amenityId'], 'Amenity Id:'),
						array('action' => 'edit', $row['Amenity']['amenityId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?=$html->link("Show All Results", '/amenities/search?query='.$query)?></li>
</ul>

<?php endif; ?>