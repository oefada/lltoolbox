<?php
$this->pageTitle = 'Keywords';
?>
<?=$layout->blockStart('toolbar');?>
	<?php echo $html->link(__('<span><b class="icon"></b>Add New Keyword</span>', true), array('action'=>'add'), array('class' => 'button add'), false, false); ?>
<?=$layout->blockEnd();?>

<div class="searchRedirects index">


<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>

<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('site');?></th>
	<th><?php echo $paginator->sort('keyword');?></th>
	<th><?php echo $paginator->sort('Redirect URL','redirectUrl');?></th>
	<th><?php echo $paginator->sort('Display Blurb?','displayBlurb');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($searchRedirects as $searchRedirect):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $multisite->indexDisplay('SearchRedirect', $searchRedirect['SearchRedirect']['sites']); ?>
		</td>
		<td>
			<?php echo $searchRedirect['SearchRedirect']['keyword']; ?>
		</td>
		<td>
			<?php echo $searchRedirect['SearchRedirect']['redirectUrl']; ?>
		</td>
		<td>
			<?php echo $searchRedirect['SearchRedirect']['displayBlurb']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $searchRedirect['SearchRedirect']['searchRedirectId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $searchRedirect['SearchRedirect']['searchRedirectId']), null, sprintf(__('Are you sure you want to delete %s?', true), $searchRedirect['SearchRedirect']['keyword'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>

<?php echo $this->renderElement('ajax_paginator'); ?>