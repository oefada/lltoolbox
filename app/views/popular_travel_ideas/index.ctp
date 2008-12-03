<div class="popularTravelIdeas index">
	<div id='query_form'>
	<form accept-charset="UNKNOWN" enctype="application/x-www-form-urlencoded" method="get">
	<label for="query">Find a Popular Travel Idea by style, keyword, or name.</label>
	<input id="query" maxlength="2147483647" name="query" size="20" type="text" value="" />
	<div id="loading" style="display: none; float: left"><?php echo $html->image("ajax-loader.gif") ?></div>
	</form>
	</div>
	<?php
	$options = array(
		'update' => 'auto_complete',
		'url'    => '/popular_travel_ideas/search',
		'frequency' => 1,
		'loading' => "Element.hide('auto_complete');Element.show('loading')",
		'complete' => "Element.hide('loading');Effect.Appear('auto_complete')"
	);

	print $ajax -> observeField('query', $options);
	?>
	<div id="auto_complete" class="auto_complete">
		<!-- Results will load here --></div>
	
<h2><?php __('PopularTravelIdeas');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('popularTravelIdeaId');?></th>
	<th><?php echo $paginator->sort('styleId');?></th>
	<th><?php echo $paginator->sort('popularTravelIdeaName');?></th>
	<th><?php echo $paginator->sort('linkToMultipleStyles');?></th>
	<th><?php echo $paginator->sort('keywords');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($popularTravelIdeas as $popularTravelIdea):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaId']; ?>
		</td>
		<td>
			<?php echo $popularTravelIdea['Style']['styleId'].' - '.$popularTravelIdea['Style']['styleName']; ?>
		</td>
		<td>
			<?php echo $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaName']; ?>
		</td>
		<td>
			<?php echo $popularTravelIdea['PopularTravelIdea']['linkToMultipleStyles']; ?>
		</td>
		<td>
			<?php echo $popularTravelIdea['PopularTravelIdea']['keywords']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaId'])); ?>
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
		<li><?php echo $html->link(__('New PopularTravelIdea', true), array('action'=>'add')); ?></li>
	</ul>
</div>
