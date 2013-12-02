<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link($row['Press']['pressDesc']."<br />Client ID: ".$row['Press']['clientId'].', '.$row['Press']['pressDate'],
						array('action' => 'edit', $row['Press']['pressId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?=$html->link("Show All Results", '/presses/search?query='.$query)?></li>
</ul>

<?php endif; ?>