<style type="text/css">
.input { margin-bottom: 10px; }
table { border: none; }
table label { float: left; }
table tr { margin-bottom: 5px; }
table tr th { text-decoration: underline; }
table tr td { border: none; }
.input { margin: 0; }
#sort-table .input-row input { width: 250px; }
</style>

<script type="text/javascript" src="/js/tablednd.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sort-table').tableDnD();
});

function addRow() {
	var row = jQuery('#sort-table .input-row:first').clone();
	var numRows = jQuery('#sort-table .input-row').length;
	var clientUrl = jQuery('#add-client-url').val();
	jQuery('#add-client-url').val('');
	row.attr('id', 'r'+(numRows+1));
	row.children('td').children('input').val('');
	row.children('td').children('.inactive').removeAttr("checked");
	row.children('td').children('.inactive').val('1');
	row.children('td').children('.client-url').val(clientUrl);
	jQuery('#sort-table .input-row:last').after(row);
	jQuery('#sort-table #r'+(numRows+1)+' .remove-link').attr('onclick', '');
	jQuery('#sort-table #r'+(numRows+1)+' .remove-link').click(function() {
		deleteRow('r'+(numRows+1));
	});
	jQuery('#sort-table').tableDnD();
}

function deleteRow(rowId) {
	if (jQuery('#sort-table .input-row').length > 1) {
		jQuery('#'+rowId+'').remove();
	} else {
		alert('Must have at least one slide!');
	}
}
</script>



<h2><?=$tabName?></h2>

<? if (isset($header[$tabName]['algorithm']) && !empty($header[$tabName]['algorithm'])) : ?>
	Algorithm: <?=$header[$tabName]['algorithm']?>
<? else: ?>

<div style="float: left;">
Select a date to schedule:<br />
<form name="schedule-date" method="POST" action="#">
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
	<? for ($i=2012; $i<=2014; $i++) : ?>
	<option value="<?=$i?>" <? if (@$year == $i) echo 'SELECTED'; ?> ><?=$i;?></option>
	<? endfor; ?>
</select>
<input type="submit" value="Go" />
</form>
</div>

<div style="float: left; margin-left: 35px; margin-top: 10px;">
<? if (isset($others['current']['startDate'])) : ?>
Currently scheduled date: <?=$others['current']['startDate'];?><br />
<? endif; ?>
<? if (isset($others['next']['startDate'])) : ?>
Next scheduled date: <?=$others['next']['startDate'];?><br />
<? endif; ?>
</div>
<? if (isset($scheduleDate)) : ?>
<div style="float: left; margin-left: 35px; margin-top: 15px;">
	<input type="submit" value="Preview" onClick="window.open('http://www.luxurylink.com/?pDate=<?=$scheduleDate?>'); return false;" />
</div>
<? endif; ?>
<div style="clear: both;"></div>

<? if (isset($scheduleDate)) : ?>
<br />
Scheduling for <?=$scheduleDate?>
<br /><br />

<? if (isset($dataSaved) && $dataSaved) : ?>
<center><h3>Data Saved</h2=3></center>
<br /><br />
<? endif; ?>
<form name="tabs" method="POST" action="#">
<input type="hidden" name="tab-data" value="1" />
<input type="hidden" name="schedule-date" value="<?=$scheduleDate?>" />
<input type="hidden" name="month" value="<?=$month?>" />
<input type="hidden" name="year" value="<?=$year?>" />
<input type="hidden" name="day" value="<?=$day?>" />

<table id="sort-table">
	<tr id="header-row" class="nodrag nodrop">
		<th>Client URL</th>
		<th>Package ID (optional)</th>
		<th>Remove</th>
	</tr>

	<?
	if (isset($currData) && is_array($currData) && count($currData) > 0) :
	?>
		<? 
		$i = 0;
		foreach ($currData AS $client) : 
		$i++;
		?>
	<tr id="r<?=$i?>" class="input-row">
		<td>
			<input type="text" name="clientUrl[]" class="client-url" value="<?=@$client['clientUrl']?>" />
		</td>
		<td class="input-col">
			<input type="text" name="packageId[]" value="<?=@$client['packageId']?>" />
		</td>
		<td><a class="remove-link" onclick="deleteRow('r<?=$i?>'); return false;"><img src="/img/x.png"></a></td>
	</tr>
		<? endforeach; ?>
	<?
	else :
	?>
	<tr id="r1" class="input-row">
		<td>
			<input type="text" name="clientUrl[]" class="client-url" />
		</td>
		<td class="input-col">
			<input type="text" name="packageId[]" />
		</td>
		<td><a class="remove-link" onclick="deleteRow('r1'); return false;"><img src="/img/x.png"></a></td>
	</tr>
	<? endif; ?>
	
	<tr class="nodrag nodrop">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input type="submit" value="Save" /></td>
	</tr>
</table>
</form>

<br />
Add New Client:
<br />
<div style="float: left;">
	<fieldset>
		<div class="input">
			<label>Client URL</label>
			<input id="add-client-url" type="text" />
		</div>
	</fieldset>
</div>
<div style="float: left; margin-top: 5px;">
	<input type="submit" value="Add" onclick="addRow(); return false;" />
</div>

<? else: ?>
<center><h3>Select a date to schedule</h3></center>
<? endif; ?>

<? endif; ?>