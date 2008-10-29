<?php if (isset($results) && count($results) > 0): ?>
<ul id="as_ul">
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$text->highlight(
											$text->excerpt($row['User']['firstName']." ".$row['User']['lastName'], $query, 20),
											$query
										)."<br />".$html2->c($row['User']['userId'], 'User Id:'),
						array('action' => 'view', $row['User']['userId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
</ul>
<?php endif; ?>