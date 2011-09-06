<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$text->excerpt($row['AjaxSearch']['name'], $query, 20),
											$query
										)."<br />".$html2->c($row['AjaxSearch']['clientId'], 'Client Id:'),
						array('controller' => 'clients','action' => 'view', $row['AjaxSearch']['clientId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?=$html->link("Show All Results", '/clients/search?query='.$query)?></li>
</ul>

<?php endif; ?>