<div class="paging-container">
	<?php if (isset($showCount) && $showCount === true): ?>
		<div class="paging-counter" style="float: right; clear: both;">
		<?php
		echo $paginator->counter(array(
		'format' => __('%count% matches | %start% - %end% displayed', true)
		));
		?>
		</div>
	<?php endif ?>
<div class="paging">
<?php
if (isset($divToPaginate) && $divToPaginate != false) {
echo $paginator->options(array('update' => $divToPaginate, 'indicator' => 'spinner', 'url' => $this->params['form']));
} else {
	echo $paginator->options(array('url' => $this->params['form']));
}
echo $paginator->prev('<< Prev', array('class' => 'nextprev'), null, array('class' => 'disabled'));
echo $paginator->numbers(array('first' => ' <<< ', 'last' => '>>>', 'separator' => ''));
echo $paginator->next('Next >>', array('class' => 'nextprev'), null, array('class' => 'disabled'));
?>
</div>
</div>