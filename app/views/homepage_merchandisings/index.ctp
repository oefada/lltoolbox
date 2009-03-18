<div class="homepageMerchandisings index">
<h2><?php __('HomepageMerchandisings');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('homepageMerchandisingId');?></th>
	<th><?php echo $paginator->sort('homepageMerchandisingTypeId');?></th>
	<th><?php echo $paginator->sort('packageId');?></th>
	<th><?php echo $paginator->sort('title');?></th>
	<th><?php echo $paginator->sort('linkText');?></th>
	<th><?php echo $paginator->sort('linkUrl');?></th>
	<th><?php echo $paginator->sort('html');?></th>
	<th><?php echo $paginator->sort('inactive');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($homepageMerchandisings as $homepageMerchandising):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingId']; ?>
		</td>
		<td>
			<?php echo $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingTypeId']; ?>
		</td>
		<td>
			<?php echo $homepageMerchandising['HomepageMerchandising']['packageId']; ?>
		</td>
		<td>
			<?php echo $homepageMerchandising['HomepageMerchandising']['title']; ?>
		</td>
		<td>
			<?php echo $homepageMerchandising['HomepageMerchandising']['linkText']; ?>
		</td>
		<td>
			<?php echo $homepageMerchandising['HomepageMerchandising']['linkUrl']; ?>
		</td>
		<td>
			<?php echo $homepageMerchandising['HomepageMerchandising']['html']; ?>
		</td>
		<td>
			<?php echo $homepageMerchandising['HomepageMerchandising']['inactive']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $homepageMerchandising['HomepageMerchandising']['homepageMerchandisingId'])); ?>
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
		<li><?php echo $html->link(__('New HomepageMerchandising', true), array('action'=>'add')); ?></li>
	</ul>
</div>
