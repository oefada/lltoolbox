<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link($row['Accolade']['description']."<br />Client ID: ".$row['Accolade']['clientId'].', '.$row['AccoladeSource']['accoladeSourceName'],
						array('action' => 'edit', $row['Accolade']['accoladeId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
	<li class="showAll"><?=$html->link("Show All Results", '/clients/search?query='.$query)?></li>
</ul>

<?php endif; ?>