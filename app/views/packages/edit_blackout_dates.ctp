<?php $this->layout = 'overlay_form'; ?>
<script type="text/javascript">
    var packageId = <?=$packageId;?>;
    var clientId = <?=$clientId;?>;
	var counter = <?=$blackout_count;?>;
	function disable_date_range(id) {
		$('#date-range-div-' + id).hide();
		$('#date_range_delete_' + id).val(1);
	}
	function add_blackout_range() {
		$('#date-range-div-' + counter).show();
		counter++;
	}
</script>

<?php echo $html->css('jquery.autocomplete'); 
echo $javascript->link('jquery/jquery-autocomplete/jquery.autocomplete'); ?>
<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>


<table id="validity" class="package-summary room-night">
<tr class="odd">
	<th>Valid for Travel</th>
	<td>
	<?php if (!empty($current_validity)) {
		foreach ($current_validity as $v) {
			echo $v . '<br />';
		}
      }
	?>
	</td>
</tr>
<tr>
	<th>Blackout Dates</th>
	<td>
	<?php if (!empty($current_blackout)) {
		foreach ($current_blackout as $v) {
			echo $v . '<br />';
		}
      }
	?>
	</td>
</tr>
</table>


<form id="edit_blackout">
	<h3 style="margin-bottom;15px;">Recurring Blackout Days</h3>
	<div style="margin-top:15px;margin-bottom:15px;">
	<?php foreach ($weekdays as $wd):?>
		<?php $checked = stristr($blackout_weekday, $wd) ? 'checked="checked"' : '';?>
		<input type="checkbox" name="data[PackageBlackoutWeekday][]" <?=$checked;?> value="<?=$wd;?>" /> <?=$wd;?>&nbsp;&nbsp;
	<?php endforeach;?>
	</div>

	<h3 style="margin-bottom:15px;">Blackout Ranges</h3>
	<div style="margin-top:15px;margin-bottom:15px;">

	<?php foreach ($blackout as $k =>$bo) :?>
	<div style="padding:5px;" id="date-range-div-<?=$k;?>">
    <input type="text" size="13" id="bo-pair-start-<?=$k;?>" class="datepicker startdate" name="data[PackageBlackout][<?php echo $k; ?>][startDate]" value="<?php echo (!empty($bo['packageBlackout']['startDate'])) ? date('M j Y', strtotime($bo['packageBlackout']['startDate'])) : ''; ?>" />
    <input type="text" style="margin-left:30px;" size="13" id="bo-pair-end-<?=$k;?>" class="datepicker" name="data[PackageBlackout][<?php echo $k; ?>][endDate]" value="<?php echo (!empty($bo['packageBlackout']['endDate'])) ? date('M j Y', strtotime($bo['packageBlackout']['endDate'])) : ''; ?>" />
	<input type="hidden" id="date_range_delete_<?=$k;?>" name="data[PackageBlackout][<?php echo $k;?>][delete]" value="0" />
	<span style="cursor:pointer;margin-left:15px;text-decoration:underline;" onclick="disable_date_range(<?=$k;?>);">[x]</span>
	</div>
	<?php endforeach;?>

	<?php for ($k = $blackout_count; $k < ($blackout_count + 20); $k++):?>
	<div style="padding:5px;display:none;" id="date-range-div-<?=$k;?>">
    <input type="text" size="13" id="bo-pair-start-<?=$k;?>" class="datepicker startdate" name="data[PackageBlackout][<?php echo $k; ?>][startDate]" />
    <input type="text" style="margin-left:30px;" size="13" id="bo-pair-end-<?=$k;?>" class="datepicker" name="data[PackageBlackout][<?php echo $k; ?>][endDate]" />
	<input type="hidden" id="date_range_delete_<?=$k;?>" name="data[PackageBlackout][<?php echo $k;?>][delete]" value="0" />
	<span style="cursor:pointer;margin-left:15px;text-decoration:underline;" onclick="disable_date_range(<?=$k;?>);">[x]</span>
	</div>
	<?php endfor;?>

	</div>
	<span style="cursor:pointer;text-decoration:underline;" onclick="add_blackout_range();">Add Blackout</span>
	<br /><br />
    <input type="button" value="Save Changes" onclick="submitForm('edit_blackout');" />
</form>

<script>

/*
$('.startdate').datepicker({
		onSelect: function(dateText, inst) {

		}
});
*/
</script>
