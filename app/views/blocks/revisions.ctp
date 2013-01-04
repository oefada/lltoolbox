<?php
$this->pageTitle = 'Block Revisions';
$this->set('hideSidebar', true);
?>

<script type="text/javascript">
	window.activateRevision = function($block, $rev) {
		if (confirm('Are you sure you wish to copy this revision into the editor? (You will still have to Publish before it goes live)')) {
			jQuery.ajax({
				'url' : '/blocks/revisions/' + $block + '/activate:' + $rev,
				'cache' : false,
				'data' : {
					'xhr' : true
				},
				'dataType' : 'html',
				'success' : function(d, t, j) {
					window.location.href = '/blocks/edit/' + $block;
				},
				'type' : 'POST'
			});
		}
		return false;
	}
</script>

<style>
	span.revisionstatus {
		display: inline-block;
		width: 16px;
		height: 16px;
		margin: 0;
		padding: 0;
		background-position: 0 0;
		background-repeat: no-repeat;
		overflow: hidden;
	}
	span.revisionstatus.enabled {
		background-image: url('http://ui.llsrv.us/images/icons/silk/accept.png');
	}
	span.revisionstatus.disabled {
		background-image: url('http://ui.llsrv.us/images/icons/silk/plugin_disabled.png');
	}
	span.revisionstatus.disabled:hover {
		background-image: url('http://ui.llsrv.us/images/icons/silk/add.png');
	}
</style>

<div>
	<ul>
		<li>Revisions for: <b><?php echo $blockPageUrl; ?></b></li>
		<li><?php echo $html->link('Edit Block', array(
				'action' => 'edit',
				$blockPageId
			));
		?></li>
		<li><?php echo $html->link('List all Blocks', array('action' => 'index')); ?></li>
			<li><?php echo $html->link('Garbage Collect', array(
				'action' => 'garbage',
				$blockPageId,
			));
 ?></li>
	</ul>
</div>

<br>

<div style="width: 700px;">
	<table>
		<tr>
			<th>Revision</th>
			<th>Hash</th>
			<th>Active</th>
			<th>Editor</th>
			<th>Date</th>
		</tr>
		<?php foreach ($blockRevisions as $br): ?>
			<tr>
				<td><?php echo $br['BlockRevision']['blockRevisionId']; ?></td>
				<td><?php echo $html->link($br['BlockRevision']['sha1'], $br['BlockRevision']['previewUrl'], array('target' => '_blockPreview')); ?></td>
				<td><?php

				if ($br['BlockRevision']['active']) {
					echo '<span class="revisionstatus enabled"></span>';
				} else {
					echo $html->link('<span class="revisionstatus disabled"></span>', '#', array(
						'escape' => false,
						'onclick' => "return activateRevision(" . $blockPageId . "," . $br['BlockRevision']['blockRevisionId'] . ")"
					));
				}
					?></td>
				<td><?php echo $br['BlockRevision']['editor']; ?></td>
				<td><?php echo $br['BlockRevision']['created']; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
