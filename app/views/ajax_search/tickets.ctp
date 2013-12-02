<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$row['SimpleModel']['username']."<br />".$html2->c($row['AjaxSearch']['ticketId'], 'Ticket Id:'),
						array('controller' => 'tickets', 'action' => 'index/?query=' . $row['AjaxSearch']['ticketId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
</ul>

<?php endif; ?>