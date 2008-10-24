<div class="as_header">
	<div class="as_corner"></div>
	<div class="as_bar"></div>
</div>
<?php if (isset($results) && count($results) > 0): ?>
	<h2>Search Results</h2>
<ul id="as_ul">
<?php foreach($results as $row): ?>
	<li><?=$html->link($text->highlight($text->excerpt($row['Client']['name'], $query, 10), $query), array('action' => 'view', $row['Client']['clientId']), null, false , false ); ?></li>
<?php endforeach;?>
</ul>
<?php endif; ?>
<div class="as_footer">
		<div class="as_corner"></div>
		<div class="as_bar"></div>
</div>