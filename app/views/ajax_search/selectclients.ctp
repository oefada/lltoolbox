<?php if (isset($results) && count($results) > 0): ?>
<ul id="as_ul">
<?php foreach($results as $row): ?>
	<li><?=$html->link($row['AjaxSearch']['name']."<br />".$html2->c("<span class='inputtable'>".$row['AjaxSearch']['clientId']."</span>", 'Client Id:'),
						array('controller' => 'clients', 'action' => 'view', $row['AjaxSearch']['clientId']),
						array('class' => 'inputplace'),
						false ,
						false ); ?>
		
	</li>
<?php endforeach;?>
</ul>
<?php endif; ?>