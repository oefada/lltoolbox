<div class="menus index">
	<?php /* File: /app/views/people/index.ctp */?>
	<div id='query_form'>
	<form accept-charset="UNKNOWN" enctype="application/x-www-form-urlencoded" method="get">
	<label for="query">Find a Menu/Style</label>
	<input id="query" maxlength="2147483647" name="query" size="20" type="text" value="" />
	<div id="loading" style="display: none; float: left"><?php echo $html->image("ajax-loader.gif") ?></div>
	</form>
	</div>
	<?php
	$options = array(
		'update' => 'auto_complete',
		'url'    => '/styles/search',
		'frequency' => 1,
		'loading' => "Element.hide('auto_complete');Element.show('loading')",
		'complete' => "Element.hide('loading');Effect.Appear('auto_complete')"
	);

	print $ajax -> observeField('query', $options);
	?>
	<div id="auto_complete" class="auto_complete">
		<!-- Results will load here --></div>
<h2><?php __('Menus');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<?php
$i = 0;
foreach ($styles as $style):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
<dl<?=$class?>>
	<?= $html->link($style['Style']['styleName'], array('action' => 'edit_by_style', 'id' => $style['Style']['styleId'])) ?>
	<?php if($style['Style']['styleInactive']) echo '(inactive)'; ?>
</dl>
<?php endforeach; ?>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
