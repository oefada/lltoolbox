<style type="text/css">
.input { margin-bottom: 10px; }
table { border: none; }
table label { float: left; }
table tr { margin-bottom: 5px; }
table tr th { text-decoration: underline; }
table tr td { border: none; }
.input { margin: 0; }
.input-row td { padding: 25px 0 25px; }
.input-col label { float: left; width: 80px; text-align: right; margin-right: 10px; margin-bottom: 4px; margin-top: 2px; clear: left; }
.input-col input { float: left; width: 350px; margin-bottom: 4px; }
form { clear: none; }
</style>

<script type="text/javascript" src="/js/tablednd.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	setRow('r1');
});

function addRow() {
	var row = jQuery('#sort-table .input-row:first').clone();
	var numRows = jQuery('#sort-table .input-row').length;
	row.attr('id', 'r'+(numRows+1));
	jQuery('#sort-table .input-row:last').after(row);

	jQuery('#r'+(numRows+1)+' .input-col .image-url').val(jQuery('#add-image-url').val());
	jQuery('#add-image-url').val('');
	
	setRow('r'+(numRows+1));
}

function setRow(rowId) {
	jQuery('#'+rowId+' .remove-link').click(function() {
		if (jQuery('#sort-table .input-row').length > 1) {
			jQuery('#'+rowId+'').remove();
		} else {
			alert('Must have at least one slide!');
		}
	});
	jQuery('#sort-table').tableDnD();
}
</script>


<h3 style="font-size: 16px; padding: 0;">Featured Auction</h3>
<br />

<div style="float: left; margin: 5px 0 0 15px;">
	Select Site:
</div>
<div style="float: left; margin-left: 10px;">
	<select>
		<option>Luxury Link</option>
		<option>Family Getaway</option>
	</select>
</div>
<div style="clear: both;"></div>
<br />
<br />


<h2>Featured Auction Scheduling</h2>

Select a date to schedule:

<form name="schedule-date" method="POST" action="/merchandising/fauction">
<input type="hidden" name="schedule-date" value="1" />
<select name="month">
	<option value="0">Month</option>
	<? for ($i=1; $i<=12; $i++) : ?>
	<option value="<?=$i?>" <? if (@$month == $i) echo 'SELECTED'; ?> ><?=date('F', mktime(0, 0, 0, $i));?></option>
	<? endfor; ?>
</select>
<select name="day">
	<option value="0">Day</option>
	<? for ($i = 1; $i <= 31; $i++) : ?>
	<option value="<?=$i;?>" <? if (@$day == $i) echo 'SELECTED'; ?> ><?=$i;?></option>
	<? endfor; ?>
</select>
<select name="year">
	<option value="0">Year</option>
	<? for ($i=2011; $i<=2014; $i++) : ?>
	<option value="<?=$i?>" <? if (@$year == $i) echo 'SELECTED'; ?> ><?=$i;?></option>
	<? endfor; ?>
</select>
<input type="submit" value="Go" />
</form>


<br /><br />

<? if (!isset($scheduleDate)) : ?>
	<center><h3>Please select a date to schedule</h3></center>
	<br /><br />
<? else : ?>
	<? if (isset($dataSaved) && $dataSaved) : ?>
	<center><h3>Changes have been saved</h3></center>
	<? endif; ?>
<span style="font-size: 14px;">Scheduling Featured Auction for <?=$scheduleDate?></span>
<br /><br />




<form name="fauction" method="POST" action="/merchandising/fauction">
	<input type="hidden" name="schedule-date" value="<?=$scheduleDate?>" />
	<input type="hidden" name="month" value="<?=$month?>" />
	<input type="hidden" name="year" value="<?=$year?>" />
	<input type="hidden" name="day" value="<?=$day?>" />
	<input type="hidden" name="fauction" value="1" />


<table id="sort-table">
	<tr id="header-row" class="nodrag nodrop">
		<th>Client URL</th>
		<th>Remove</th>
	</tr>
	
	<?
	if (isset($currData['clients']) && is_array($currData['clients']) && count($currData['clients']) > 0) :
		$i = 0;
		foreach ($currData['clients'] AS $client) :
		$i++;
	?>
	<tr id="r<?=$i?>" class="input-row">
		<td class="input-col">
			<input type="text" name="clientUrl[]" class="image-url" value="<?=@$client['linkUrl']?>" />
		</td>
		<td><a class="remove-link"><img src="/img/x.png"></a></td>
	</tr>
	<? 
		endforeach;
	else :
	?>
	
	<tr id="r1" class="input-row">
		<td class="input-col">
			<input type="text" name="clientUrl[]" class="image-url" value="" />
		</td>
		<td><a class="remove-link"><img src="/img/x.png"></a></td>
	</tr>
	<? endif; ?>
	
	
	<tr class="nodrag nodrop">
		<td>&nbsp;</td>
		<td><br /><input type="submit" value="Save" /></td>
	</tr>
</table>
</form>

<br />
Add New Showcase:
<br />
<div style="float: left;">
	<fieldset>
		<div class="input">
			<label>Showcase URL</label>
			<input id="add-image-url" type="text" />
		</div>
	</fieldset>
</div>
<div style="float: left; margin-top: 5px;">
	<input type="submit" value="Add" onclick="addRow(); return false;" />
</div>

<? endif; ?>
