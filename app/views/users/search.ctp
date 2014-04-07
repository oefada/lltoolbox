<?php if (isset($results) && count($results) > 0): ?>
<ul id="as_ul">
<?php foreach($results as $row): ?>
	<li><?=$html->link($row['User']['firstName']." ".$row['User']['lastName']."<br />".$html2->c($row['User']['userId'], 'User Id:'),
						array('action' => 'view', $row['User']['userId']),
						null,
						false ,
						false ); ?>

	</li>
<?php endforeach;?>
</ul>
<?php endif; ?>
