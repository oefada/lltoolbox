<div class="paging">
<?php
echo $paginator->options(array('update' => $divToPaginate, 'indicator' => 'spinner'));
echo $paginator->prev('<< Prev', null, null, array('class' => 'disabled'));
echo $paginator->numbers(array('first' => ' <<< ', 'last' => '>>>', 'separator' => ''));
echo $paginator->next('Next >>', array('class' => 'nextprev'), null, array('class' => 'disabled'));
?>
</div>