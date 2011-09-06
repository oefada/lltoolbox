<?php if (isset($results) && count($results) > 0): ?>
<ul id="as_ul">
<?php foreach($results as $row): ?>
	<li><?=$html->link($row['AjaxSearch']['firstName']." ".$row['AjaxSearch']['lastName']."<br />".$html2->c($row['AjaxSearch']['userId'], 'User Id:'),
						array('controller' => 'users', 'action' => 'view', $row['AjaxSearch']['userId']),
						null,
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
</ul>
<?php endif; ?>