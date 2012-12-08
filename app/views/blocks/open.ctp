<?php
$this->pageTitle = 'Block';
$this->set('hideSidebar', true);
?>
<h2>Revisions for: <?php echo $blockPageUrl; ?></h2>
<div>
	<table>
		<tr>
			<th>id</th>
			<th>created</th>
			<th>modified</th>
			<th>active</th>
		</tr>
		<?php foreach ($BlockRevisions as $br):
		?>
		<tr>
			<td><?php echo $html->link($br['BlockRevision']['blockRevisionId'] . '-' . substr($br['BlockRevision']['sha1'],0,7), array('action'=>'edit', $br['BlockRevision']['blockRevisionId'])); ?>
			<td><?php echo $br['BlockRevision']['created']; ?></td>
			<td><?php echo $br['BlockRevision']['modified']; ?></td>
			<td><?php echo $br['BlockRevision']['active'] ? 'YES' : $html->link('Make Active',array('action'=>'activate','blockPageId'=>$blockPageId,'blockRevisionId'=>$br['BlockRevision']['blockRevisionId'],'blockSha1'=>$br['BlockRevision']['sha1'])); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
