<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$text->excerpt($row['Client']['name'], $query, 20),
											$query
										)."<br />".$html2->c($row['Client']['clientId'], 'Client Id:'),
						array('action' => 'view', $row['Client']['clientId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?=$html->link("Show All Results", '/clients/search?query='.$query)?></li>
</ul>

<?php endif; ?>