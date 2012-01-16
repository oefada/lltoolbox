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
	var tabName = jQuery('#add-tab-name').val();
	jQuery('#add-tab-name').val('');
	row.attr('id', 'r'+(numRows+1));
	row.children('td').children('input').val('');
	row.children('td').children('.inactive').removeAttr("checked");
	row.children('td').children('.inactive').val('1');
	row.children('td').children('.tab-name').val(tabName);
	jQuery('#sort-table .input-row:last').after(row);
	jQuery('#sort-table').tableDnD();
}
</script>


<? if (isset($dataSaved) && $dataSaved) : ?>
<center><h3>Data Saved</h2=3></center>
<br /><br />
<? endif; ?>
<form name="tabs" method="POST" action="/merchandising/hometabs">
<input type="hidden" name="hometabs" value="1" />

<table id="sort-table">
	<tr id="header-row" class="nodrag nodrop">
		<th>Tab Name</th>
		<th>Footer Link</th>
		<th>Footer Text</th>
		<th style="text-align: right;">Inactive</th>
	</tr>

	<?
	if (isset($currData) && is_array($currData) && count($currData) > 0) :
	?>
		<? 
		$i = 0;
		foreach ($currData AS $tab) : 
		$i++;
		?>
	<tr id="r<?=$i?>" class="input-row">
		<td>
			<input type="hidden" name="algorithm[]" value="<?=@$tab['algorithm']?>" />
			<input type="hidden" name="merchDataGroupId[]" value="<?=@$tab['merchDataGroupId']?>" />
			<input type="text" name="tabName[]" class="tab-name" value="<?=@$tab['tabName']?>" />
		</td>
		<td class="input-col">
			<input type="text" name="footerLink[]" value="<?=$tab['footerLink']?>" />
		</td>
		<td>
			<input type="text" name="footerText[]" value="<?=$tab['footerText']?>" />
		</td>
		<td align="right">
			<input type="checkbox" name="inactive-<?=$i?>" class="inactive" value="1" <? if ($tab['inactive']) echo 'CHECKED'; ?> style="width: 10px;" />
		</td>
	</tr>
		<? endforeach; ?>
	<?
	else :
	?>
	<tr id="r1" class="input-row">
		<td>
			<input type="hidden" name="merchDataGroupId[]" value="" />
			<input type="hidden" name="algorithm[]" value="" />
			<input type="text" name="tabName[]" />
		</td>
		<td class="input-col">
			<input type="text" name="footerLink[]" />
		</td>
		<td>
			<input type="text" name="footerText[]" />
		</td>
		<td>
			<input type="checkbox" name="inactive-1" class="inactive" value="1" style="width: 10px;"/>
		</td>
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
Add New Tab:
<br />
<div style="float: left;">
	<fieldset>
		<div class="input">
			<label>Tab Name</label>
			<input id="add-tab-name" type="text" />
		</div>
	</fieldset>
</div>
<div style="float: left; margin-top: 5px;">
	<input type="submit" value="Add" onclick="addRow(); return false;" />
</div>
