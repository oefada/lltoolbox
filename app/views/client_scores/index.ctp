<div class="clientScores index">
<h2><?php __('Client Scores');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('clientScoreId');?></th>
	<th><?php echo $paginator->sort('clientScoreTypeId');?></th>
	<th><?php echo $paginator->sort('clientId');?></th>
	<th><?php echo $paginator->sort('score');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($clientScores as $clientScore):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $clientScore['ClientScore']['clientScoreId']; ?>
		</td>
		<td>
			<?php echo $clientScore['ClientScoreType']['clientScoreTypeName']; ?>
		</td>
		<td>
			<?php echo $html->link($clientScore['Client']['name'], array('controller'=> 'clients', 'action'=>'view', $clientScore['Client']['clientId'])); ?>
		</td>
		<td>
			<?php echo $clientScore['ClientScore']['score']; ?>
		</td>
		<td class="actions">
			<?php
					echo $html->link('Edit',
						'/client_scores/edit/' . $clientScore['ClientScore']['clientScoreId'],
						array(
							'title' => 'Edit Client Score',
							'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
							'complete' => 'closeModalbox()'
							),
						null,
						false
						);
			?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $clientScore['ClientScore']['clientScoreId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $clientScore['ClientScore']['clientScoreId'])); ?>
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
		<li>
			<?php
				echo $html->link('New Client Score',
					'/client_scores/add/',
					array(
						'title' => 'Add Client Score',
						'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
						'complete' => 'closeModalbox()'
						),
					null,
					false
				);
			?>
		</li>
	</ul>
</div>
