<h2>Nile Guide Attraction</h2>
<?php if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']): ?>
<div>Found on: <?php echo $html->link($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_REFERER']); ?></div>
<br/>
<?php endif; ?>
<table style="width:600px">
	<?php $alt=1; foreach($attraction['NileGuideAttraction'] as $k=>$v): $alt=1-$alt; ?>
		<tr <?php echo $alt?'':'class="altrow"'; ?>>
			<td><?php echo $k; ?></td>
			<td><?php echo $v; ?></td>
		</tr>
	<?php endforeach; ?>
</table>
