<h2>Nile Guide Attraction</h2>
<?php if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] && (strpos($_SERVER['HTTP_REFERER'],$_SERVER['SERVER_NAME'])===false)): ?>
<div>Found on: <?php echo $html->link($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_REFERER']); ?></div>
<br/>
<?php endif; ?>

<?php

echo $form->create('NileGuideAttraction', array('type'=>'post', 'url'=>'/nile_guide/attraction/'.$attraction['NileGuideAttraction']['ngId']));
if ($attraction['NileGuideAttraction']['publish']) {
	echo $form->submit('DELIST',array('style'=>'background-color: red;', 'onclick'=>'return (prompt("Are you sure you want to delist this attraction?  Type DELIST in this box to continue.")==="DELIST");'));
} else {
	echo '<h3>This attraction is currently DISABLED on the site.</h3>';
	echo $form->submit('Enable',array('style'=>'background-color: green;'));
}
echo $form->hidden('id', array('value'=>$attraction['NileGuideAttraction']['id']));
echo $form->hidden('publish', array('value'=>($attraction['NileGuideAttraction']['publish'] ? 0 : 1)));
echo $form->end();
?>

<table style="width:600px">
	<?php $alt=1; foreach($attraction['NileGuideAttraction'] as $k=>$v): $alt=1-$alt; ?>
		<tr <?php echo $alt ? '' : 'class="altrow"'; ?>>
			<td><?php echo $k; ?></td>
			<td><?php echo $v; ?></td>
		</tr>
	<?php endforeach; ?>
</table>
