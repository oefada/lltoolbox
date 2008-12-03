<div class="popularTravelIdeas view">
<h2><?php  __('PopularTravelIdea');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PopularTravelIdeaId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Style'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($popularTravelIdea['Style']['styleName'], array('controller'=> 'styles', 'action'=>'view', $popularTravelIdea['Style']['styleId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PopularTravelIdeaName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LinkToMultipleStyles'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $popularTravelIdea['PopularTravelIdea']['linkToMultipleStyles']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Keywords'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $popularTravelIdea['PopularTravelIdea']['keywords']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit PopularTravelIdea', true), array('action'=>'edit', $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaId'])); ?> </li>
		<li><?php echo $html->link(__('Delete PopularTravelIdea', true), array('action'=>'delete', $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $popularTravelIdea['PopularTravelIdea']['popularTravelIdeaId'])); ?> </li>
		<li><?php echo $html->link(__('List PopularTravelIdeas', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New PopularTravelIdea', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Styles', true), array('controller'=> 'styles', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Style', true), array('controller'=> 'styles', 'action'=>'add')); ?> </li>
	</ul>
</div>
