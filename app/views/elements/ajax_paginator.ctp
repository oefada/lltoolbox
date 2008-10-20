<div class="paging-container">
<div class="paging">
	<?php if (isset($showCount) && $showCount === true): ?>
		<p style="text-align: right; color: #000">
		<?php
		echo $paginator->counter(array(
		'format' => __('%count% matches | %start% - %end% displayed', true)
		));
		?></p>
	<?php endif ?>
<?php
echo $paginator->options(array('update' => $divToPaginate, 'indicator' => 'spinner', 'url' => $this->params['form']));
echo $paginator->prev('<< Prev', array('class' => 'nextprev'), null, array('class' => 'disabled'));
echo $paginator->numbers(array('first' => ' <<< ', 'last' => '>>>', 'separator' => ''));
echo $paginator->next('Next >>', array('class' => 'nextprev'), null, array('class' => 'disabled'));
?>
</div>
</div>