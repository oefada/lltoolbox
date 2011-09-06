<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$text->excerpt($row['AjaxSearch']['landingPageName'], $query, 20),
											$query
										)."<br />".$html2->c($row['AjaxSearch']['landingPageId'], 'ID:'),
						array('controller' => 'landing_pages','action' => 'edit', $row['AjaxSearch']['landingPageId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?= $html->link("Show All Results", '/landing_pages/search?query='.$query)?></li>
</ul>

<?php endif; ?>