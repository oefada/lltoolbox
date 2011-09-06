<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$text->excerpt($row['AjaxSearch']['keyword'], $query, 20),
											$query
										),
						array('controller' => 'search_redirects','action' => 'edit', $row['AjaxSearch']['searchRedirectId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?= $html->link("Show All Results", '/search_redirects/search?query='.$query)?></li>
</ul>

<?php endif; ?>