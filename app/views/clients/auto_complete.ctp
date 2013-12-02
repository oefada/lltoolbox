<ul>
<?php foreach($clients as $client): ?>
<li id="<?=$client['Client']['clientId']?>"><?php echo $client['Client']['name'].' ('.$client['Client']['clientId'].')'; ?></li>
<?php endforeach; ?>
</ul>
