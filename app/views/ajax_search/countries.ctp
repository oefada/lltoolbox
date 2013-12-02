<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$text->excerpt($row['AjaxSearch']['countryName'], $query, 20),
											$query
										)."<br />".$html2->c($row['AjaxSearch']['countryId'], 'Country ID:'),
						array('controller' => 'countries','action' => 'edit', $row['AjaxSearch']['countryId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?= $html->link("Show All Results", '/countries/search?query='.$query)?></li>
</ul>

<?php endif; ?>