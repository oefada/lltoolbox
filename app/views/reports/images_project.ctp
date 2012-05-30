<script>
	function recount() {
		jQuery('#imageReport tr.record:visible').each(function(i){jQuery(this).children('td:first').html(i+1);});
	}
</script>
<?php
$i = 0;
?>
<div style="text-align: right;">
	<a href="#" onclick="jQuery('#imageReport tr.record').css('display','table-row');recount();return false;">All</a>
	<a href="#" onclick="jQuery('#imageReport tr.record').css('display','none');jQuery('#imageReport tr.isDone').css('display','table-row');recount();return false;">Complete</a>
	<a href="#" onclick="jQuery('#imageReport tr.record').css('display','none');jQuery('#imageReport tr.hasOld').css('display','table-row');recount();return false;">Incomplete</a>
</div>
<?php
$totalOld = 0;
$totalNew = 0;
?>
<table style="width: 800px;" id="imageReport">
	<tr>
		<th>Count</th>
		<th>Id</th>
		<th>Client</th>
		<th>Old</th>
		<th>New</th>
		<th>Total</th>
		<th>Complete</th>
	</tr>
	<?php foreach ($reportData as $rd): ?>
		<tr class="record <?php echo ($rd[0]['old_format_count']>0?'hasOld':'').' '.($rd[0]['old_format_count']==0?'isDone':'');?>">
			<td><?php echo ++$i;?></td>
			<td><?php echo $rd['t1']['client_id'];?></td>
			<td style="width:444px; overflow: hidden;"><?php echo $html->link($rd['t1']['name'], '/clients/'.$rd['t1']['client_id'].'/images/organize');
			 ?></td>
			<td style="text-align: right;"><?php echo $rd[0]['old_format_count']; ?></td>
			<td style="text-align: right;"><?php echo $rd[0]['new_format_count']; ?></td>
			<td style="text-align: right;"><?php echo $rd[0]['total_image_count']; ?></td>
			<td style="text-align: right;"><?php echo $rd[0]['percent_complete']; ?>%</td>
			<?php
			$totalOld = $totalOld + $rd[0]['old_format_count'];
			$totalNew = $totalNew + $rd[0]['new_format_count'];
						?>
		</tr>
	<? endforeach; ?>
</table>
<pre style="font-weight: bold;">
Total Old: <?php echo number_format($totalOld); ?>

Total New: <?php echo number_format($totalNew); ?>

Total All: <?php echo number_format($totalOld + $totalNew); ?>

%Complete: <?php echo number_format(

($totalOld + $totalNew)==0
?
(100 * $totalNew / ($totalOld + $totalNew))
:
0
, 4); ?>%
</pre>