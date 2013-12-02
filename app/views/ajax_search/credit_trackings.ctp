<?php if (isset($results) && count($results) > 0): ?>
<ul id="as_ul">
<?php foreach($results as $row): ?>
	<li><?=$html->link($row['SimpleModel']['username']." ($".number_format($row['AjaxSearch']['balance'],2).")<br />".$html2->c($row['AjaxSearch']['userId'], 'User Id:'),
						array('controller' => 'credit_trackings', 'action' => 'view', $row['AjaxSearch']['userId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
</ul>
<?php endif; ?>