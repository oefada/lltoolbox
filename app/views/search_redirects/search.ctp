<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$row['SearchRedirect']['keyword'],
											$query
										)."<br />&gt; ".$html2->c($text->highlight($row['SearchRedirect']['redirectUrl'], $query)),
						array('action' => 'edit', $row['SearchRedirect']['searchRedirectId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?=$html->link("Show All Results", '/search_redirects/search?query='.$query)?></li>
</ul>

<?php endif; ?>