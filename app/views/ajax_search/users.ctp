<?php if (isset($results) && count($results) > 0): ?>
<ul id="as_ul">
<?php foreach($results as $row): ?>
	<li><?=$html->link($row['AjaxSearch']['firstName']." ".$row['AjaxSearch']['lastName']."<br />".$html2->c("<span class='inputtable'>".$row['AjaxSearch']['userId']."</span>", 'User Id:'),
						array('controller' => 'users', 'action' => 'view', $row['AjaxSearch']['userId']),
						array('class' => 'inputplace'),
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
</ul>
<?php endif; ?>