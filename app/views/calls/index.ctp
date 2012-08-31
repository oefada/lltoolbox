<div class="calls index">
<h2><?php __('Calls'); ?></h2>
<p>
<?php
echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
        <th><?php echo $paginator->sort('callId'); ?></th>
     
</tr>
<?php
$i = 0;
foreach ($calls as $call):
        $class = null;
        if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
        }
?>
  	<tr<?php echo $class; ?>>
                <td>
                    	<?php echo $call['Call']['callId']; ?>
                </td>
        </tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
        <?php echo $paginator->prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled')); ?>
 |	<?php echo $paginator->numbers(); ?>
        <?php echo $paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
</div>
<div class="actions">
        <ul>
            	<li><?php echo $html->link(__('New Call', true), array('action' => 'add')); ?></li>
        </ul>
</div>