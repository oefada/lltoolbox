<?php if (isset($results) && count($results) > 0): ?>
<ul>
    <?php foreach($results as $row): ?>
    <li>
        <?php echo $html->link(
            $row['AjaxSearch']['name']."<br/>".$html2->c($row['AjaxSearch']['clientId'], 'Client Id:'),
            array(
                'controller' => 'clients',
                'action' => 'view',
                $row['AjaxSearch']['clientId']
            ),
            null,
            false ,
            false );
        ?>
    </li>
    <?php endforeach;?>
    <li class="showAll"><?=$html->link("Show All Results", '/clients/search?query='.$query)?></li>
</ul>
<?php endif; ?>
