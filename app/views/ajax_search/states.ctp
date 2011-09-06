<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$text->excerpt($row['AjaxSearch']['stateName'], $query, 20),
											$query
										)."<br />".$html2->c($row['SimpleModel']['countryName'], 'Country:'),
						array('controller' => 'states','action' => 'edit', $row['AjaxSearch']['stateId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?= $html->link("Show All Results", '/states/search?query='.$query)?></li>
</ul>

<?php endif; ?>