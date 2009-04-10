<div class="articles index">
<h2><?php __('Articles');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('articleId');?></th>
	<th><?php echo $paginator->sort('primaryStyleId');?></th>
	<th><?php echo $paginator->sort('articleTitle');?></th>
	<th><?php echo $paginator->sort('articleAuthor');?></th>
	<th><?php echo $paginator->sort('articleBody');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($articles as $article):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $article['Article']['articleId']; ?>
		</td>
		<td>
			<?php echo $article['LandingPage']['landingPageName']; ?>
		</td>
		<td>
			<?php echo $article['Article']['articleTitle']; ?>
		</td>
		<td>
			<?php echo $article['Article']['articleAuthor']; ?>
		</td>
		<td>
			<?php echo $article['Article']['articleMetaDescription']; ?>
		</td>
		<td>
			<?php echo substr($article['Article']['articleBody'], 0, 70) . ' ... '; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $article['Article']['articleId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $article['Article']['articleId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $article['Article']['articleId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $article['Article']['articleId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Article', true), array('action'=>'add')); ?></li>
	</ul>
</div>
