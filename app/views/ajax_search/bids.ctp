<?php if (isset($results) && count($results) > 0): ?>
<ul>
<?php foreach($results as $row): ?>
	<li><?=$html->link(
						$row['SimpleModel']['username']."<br />".$html2->c($row['AjaxSearch']['bidId'], 'Bid Id:')."<br />".
						$html2->c($row['AjaxSearch']['userId'], 'User Id:'),
						array('controller' => 'bids', 'action' => 'edit', $row['AjaxSearch']['bidId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
</ul>

<?php endif; ?>