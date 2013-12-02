<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$text->excerpt("City: ".$row['AjaxSearch']['cityName'], $query, 20),
											$query
										)."<br />".$html2->c($row['AjaxSearch']['stateId'], 'State:')."<br />".$html2->c($row['SimpleModel']['countryName'], 'Country:'),
						array('controller' => 'cities','action' => 'edit', $row['AjaxSearch']['cityId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?= $html->link("Show All Results", '/cities/search?query='.$query)?></li>
</ul>

<?php endif; ?>