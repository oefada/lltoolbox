var rp_table = 'loaRpTable';

function add_rate_period() {
	append_row(rp_table);
}

function toggle_price(event) {
	if ($("LoaItemLoaItemTypeId").getValue() == 1 || $("LoaItemLoaItemTypeId").getValue() == 12) {
		toggle_rate_period_on();
	} else {
		toggle_price_on();
	}
	if ($('LoaItemLoaItemTypeId').getValue() == 12) {
		Field.disable("LoaItemMerchandisingDescription");
	} else {
		Field.enable("LoaItemMerchandisingDescription");
	}
}

function toggle_price_on() {
	Field.enable("LoaItemItemBasePrice");
	Field.enable("LoaItemPerPerson");
	
	Effect.Fade($('rate_periods'), {queue: 'end', duration: 0.5});
	Effect.Appear($('price'), {queue: 'end', duration: 0.5});
}

function toggle_rate_period_on() {
	Field.disable("LoaItemItemBasePrice");
	Field.disable("LoaItemPerPerson");
	
	Effect.Fade($('price'), {queue: 'end', duration: 0.5});
	Effect.Appear($('rate_periods'), {queue: 'end', duration: 0.5});
}

function add_date_range(rowId) {
	var current_time = new Date();
	var randomNum3 = current_time.getMilliseconds();
	var randomNum4 = Math.floor(Math.random() * 100);
	var dateId = randomNum3 + '_' + randomNum4;
		
	var date_now_start = getCurrentDate(0);
	var date_now_end = getCurrentDate(1);

	var inner_start = '<div id="rp_date_s_'+ dateId +'" style="padding:0px;margin:0px;display:none;">\
				<a href="javascript:void(0);" onclick="remove_date_range(\''+ dateId +'\')" style="font-size:11px;">[x]</a>\
				<input id="id_rp_date_s_'+ dateId +'" type="text" style="width:80px;" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemDate]['+ dateId +'][startDate]" class="dateformat-m-sl-d-sl-Y fill-grid-no-select MB_focusable rp_date_check" readonly="readonly" value="'+ date_now_start +'" onchange="setEndDate(\''+ dateId +'\');" />\
				<script>addDatePicker(\'id_rp_date_s_'+ dateId +'\');</script>\
				</div>';
	
	var inner_end = '<div id="rp_date_e_'+ dateId +'" style="padding:0px;margin:0px;display:none;">\
				<input id="id_rp_date_e_'+ dateId +'" type="text" style="width:80px;" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemDate]['+ dateId +'][endDate]" class="dateformat-m-sl-d-sl-Y fill-grid-no-select MB_focusable" readonly="readonly" value="'+ date_now_end +'" onchange="setEndDate(\''+ dateId +'\');" />\
				<script>addDatePicker(\'id_rp_date_e_'+ dateId +'\');</script>\
				</div>';

	$('rp_' + rowId + '_start').insert(inner_start);
	$('rp_' + rowId + '_end').insert(inner_end);
	$('rp_date_s_' + dateId).setStyle({ display: '' });
	$('rp_date_e_' + dateId).setStyle({ display: '' });
}

function remove_date_range(rowId) {
	var start_value = $('id_rp_date_s_' + rowId).value;
	var end_value = $('id_rp_date_e_' + rowId).value;
	
	if (start_value.replace(/^\s+|\s+$/g,"") || end_value.replace(/^\s+|\s+$/g,"")) {
		var answer = confirm("Are you sure you want to delete this date range?")
		if (!answer){
			return false;
		}	
	} 
	
	disableIt($('rp_date_s_' + rowId).childElements());
	disableIt($('rp_date_e_' + rowId).childElements());
	$('rp_date_s_' + rowId).setStyle({ display: 'none' });
	$('rp_date_e_' + rowId).setStyle({ display: 'none' });
}

function append_row(tblId)
{
	var tbl = document.getElementById(tblId);
	var numRows = tbl.rows.length;
	var current_time = new Date();
	var randomNum1 = current_time.getMilliseconds();
	var randomNum2 = Math.floor(Math.random() * 100);
	var randomNum3 = current_time.getMilliseconds();
	var randomNum4 = Math.floor(Math.random() * 100);
	var dateId = randomNum3 + '_' + randomNum4;
	var rowId = randomNum1 + '_' + randomNum2;
	var rowIdText = "rp_" + rowId;
	var newRow = tbl.insertRow(numRows);

	newRow.setAttribute('id', rowIdText);
	
	var newCell = newRow.insertCell(0);
	newCell.setAttribute('id', rowIdText + '_0');
	$(newCell).insert('<input type="text" style="width:160px;" id="rpname_'+ rowId +'" name="data[LoaItemRatePeriod]['+ rowId +'][loaItemRatePeriodName]" /><div style="margin-top:5px;padding:0px;"><a href="javascript:void(0);" onclick="delete_row(\''+ rowIdText +'\', 1);">Remove</a></div>');

	var date_now_start = getCurrentDate(0);
	var date_now_end = getCurrentDate(1);

	var newCell = newRow.insertCell(1);
	newCell.setAttribute('id', rowIdText + '_1');
	$(newCell).insert('<div id="'+ rowIdText +'_start" style="padding:0px;margin:0px;">\
							<div id="rp_date_s_'+ dateId +'" style="padding:0px;margin:0px;">\
								<a href="javascript:void(0);" onclick="remove_date_range(\''+ dateId +'\')" style="font-size:11px;">[x]</a>\
								<input id="id_rp_date_s_'+ dateId +'" type="text" style="width:80px;" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemDate]['+ dateId +'][startDate]" class="dateformat-m-sl-d-sl-Y fill-grid-no-select MB_focusable rp_date_check" readonly="readonly" value="'+ date_now_start  +'" onchange="setEndDate(\''+ dateId +'\');" />\
								<script>addDatePicker(\'id_rp_date_s_'+ dateId +'\');</script>\
							</div>\
						</div>\
						<div style="margin-top:15px;padding:0px;"><a href="javascript:void(0);" onclick="add_date_range(\''+ rowId + '\');">Another Date Range</a></div>');

	var newCell = newRow.insertCell(2);
	newCell.setAttribute('id', rowIdText + '_2');
	$(newCell).insert('<div id="'+ rowIdText + '_end" style="padding:0px;margin:0px;">\
							<div id="rp_date_e_'+ dateId +'" style="padding:0px;margin:0px;">\
								<input id="id_rp_date_e_'+ dateId +'" type="text" style="width:80px;" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemDate]['+ dateId +'][endDate]" class="dateformat-m-sl-d-sl-Y fill-grid-no-select MB_focusable" readonly="readonly" value="'+ date_now_end  +'" onchange="setEndDate(\''+ dateId +'\');" />\
								<script>addDatePicker(\'id_rp_date_e_'+ dateId +'\');</script>\
							</div>\
						</div>');

	var newCell = newRow.insertCell(3);
	newCell.setAttribute('id', rowIdText + '_3');
	var rp_price = '<div id="'+ rowIdText +'_s" class="rpDates">\
						<input type="hidden" id="'+ rowIdText +'_rpd_0" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w0]" value="1" />\
						<input type="hidden" id="'+ rowIdText +'_rpd_1" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w1]" value="1" />\
						<input type="hidden" id="'+ rowIdText +'_rpd_2" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w2]" value="1" />\
						<input type="hidden" id="'+ rowIdText +'_rpd_3" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w3]" value="1" />\
						<input type="hidden" id="'+ rowIdText +'_rpd_4" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w4]" value="1" />\
						<input type="hidden" id="'+ rowIdText +'_rpd_5" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w5]" value="1" />\
						<input type="hidden" id="'+ rowIdText +'_rpd_6" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w6]" value="1" />\
						<strong style="font-size:11px;">'+ currencyCode +'</strong>\
						<input type="text" style="width:50px;" id="'+ rowIdText +'_rpd_7" class="rp_price" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][price]" />\
						<div style="margin-top:5px;padding:0px;">\
							<a href="javascript:void(0);" onclick="toggleF(\''+  rowIdText +'\', \'s\', \'m\');">Different prices for weeknights/weekends</a>\
						</div>\
					</div>\
					<div id="'+ rowIdText +'_m" class="rpDates" style="display:none;">\
						<div class="rpDates">\
							<div id="'+ rowIdText +'_m_0" class="rpDates">\
								<input type="checkbox" id="'+ rowIdText +'_rpd_0-0" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w0]" value="1" onclick="checkDays(this.id);" checked="checked" /> <label for="'+ rowIdText +'_rpd_0-0">Su</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_1-0" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w1]" value="1" onclick="checkDays(this.id);" checked="checked" /> <label for="'+ rowIdText +'_rpd_1-0">M</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_2-0" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w2]" value="1" onclick="checkDays(this.id);" checked="checked" /> <label for="'+ rowIdText +'_rpd_2-0">Tu</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_3-0" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w3]" value="1" onclick="checkDays(this.id);" checked="checked" /> <label for="'+ rowIdText +'_rpd_3-0">W</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_4-0" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w4]" value="1" onclick="checkDays(this.id);" checked="checked" /> <label for="'+ rowIdText +'_rpd_4-0">Th</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_5-0" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w5]" value="1" onclick="checkDays(this.id);" checked="checked" /> <label for="'+ rowIdText +'_rpd_5-0">F</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_6-0" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][w6]" value="1" onclick="checkDays(this.id);" checked="checked" /> <label for="'+ rowIdText +'_rpd_6-0">Sa</label>\
								<strong style="font-size:11px;">'+ currencyCode +'</strong>\
								<input type="text" id="'+ rowIdText +'_rpd_7_0" style="width:50px;" class="rp_price" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][0][price]" />\
							</div>\
							<div id="'+ rowIdText +'_m_1" class="rpDates" style="margin-top:10px;">\
								<input type="checkbox" id="'+ rowIdText +'_rpd_0-1" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][1][w0]" value="1" onclick="checkDays(this.id);" /> <label for="'+ rowIdText +'_rpd_0-1">Su</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_1-1" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][1][w1]" value="1" onclick="checkDays(this.id);" /> <label for="'+ rowIdText +'_rpd_1-1">M</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_2-1" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][1][w2]" value="1" onclick="checkDays(this.id);" /> <label for="'+ rowIdText +'_rpd_2-1">Tu</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_3-1" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][1][w3]" value="1" onclick="checkDays(this.id);" /> <label for="'+ rowIdText +'_rpd_3-1">W</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_4-1" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][1][w4]" value="1" onclick="checkDays(this.id);" /> <label for="'+ rowIdText +'_rpd_4-1">Th</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_5-1" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][1][w5]" value="1" onclick="checkDays(this.id);" /> <label for="'+ rowIdText +'_rpd_5-1">F</label>\
								<input type="checkbox" id="'+ rowIdText +'_rpd_6-1" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][1][w6]" value="1" onclick="checkDays(this.id);" /> <label for="'+ rowIdText +'_rpd_6-1">Sa</label>\
								<strong style="font-size:11px;">'+ currencyCode +'</strong>\
								<input type="text" id="'+ rowIdText +'_rpd_7_1" style="width:50px;" class="rp_price" name="data[LoaItemRatePeriod]['+ rowId +'][LoaItemRate][1][price]" />\
							</div>\
							<div style="margin-top:5px;padding:0px;">\
								<a href="javascript:void(0);" onclick="toggleF(\''+ rowIdText +'\', \'m\',\'s\');">One price for all nights</a>\
							</div>\
						</div>\
					</div>';
	$(newCell).insert(rp_price);
	toggleF(rowIdText, 'm', 's');
}

function checkDays(id) {
	var tmp = id.split('-');
	var current = tmp[1];
	var other = (current == '0') ? '1' : '0';
	$(tmp[0] + '-' + other).checked = ($(id).checked) ? false : true;
}

function toggleF(rowIdText, disableId, enableId) {
	if (disableId == 's') {
		disableIt($(rowIdText + '_s').childElements());
		enableIt($(rowIdText + '_m_0').childElements());
		enableIt($(rowIdText + '_m_1').childElements());
	} else if (disableId == 'm') {
		disableIt($(rowIdText + '_m_0').childElements());
		disableIt($(rowIdText + '_m_1').childElements());
		enableIt($(rowIdText + '_s').childElements());
	}
	
	Effect.Fade($(rowIdText + '_' + disableId), {queue: 'end', duration: 0.2});
	Effect.Appear($(rowIdText + '_' + enableId), {queue: 'end', duration: 0.2});
}

function disableIt(arr) {
	arr.each(function(node){
		if (node.nodeName == 'INPUT') {
			Field.disable(node.id);
		}
    });
}

function enableIt(arr) {
	arr.each(function(node){
		if (node.nodeName == 'INPUT') {
			Field.enable(node.id);
		}
    });
}

function delete_row(txtIndex, confirmIt)
{
	if (confirmIt) {
		var askThem = confirm("You sure you want to remove this row?");
		if(!askThem) {
			return false;
		}
	}
	var tbl = document.getElementById(rp_table);
	var rows = document.getElementById(rp_table).rows;
	for (var i=1; i < rows.length; i++) {
		var row = rows[i];
		if (row.id == txtIndex) {
			tbl.deleteRow(i);
			return false;
		}
	}
}

function isTrim(value) {
	value = value.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	return (value) ? true : false;
}

function isNumeric(value) {
	value = value.toString().match(/^[-]?\d*\.?\d*$/)
	return (value) ? true : false;
}

var previousErrorIds = new Array();
function clearPreviousErrors() {
	for (i=1; i < previousErrorIds.length; i++) {
		$(previousErrorIds[i]).setStyle({ backgroundColor: '#FFFFFF' });
	}
}

function checkFormLoaItem() {
	var errorMsg = '';
	var errorCnt = 0;
	var errorIds = new Array();

	clearPreviousErrors();

	if (!isTrim($('LoaItemItemName').value)) {
		errorMsg = '* Name cannot be blank<br />';
		errorCnt++;
		errorIds[errorCnt] = 'LoaItemItemName';
	}

	if ($("LoaItemLoaItemTypeId").getValue() == 1 || $("LoaItemLoaItemTypeId").getValue() == 12) {
		var rows = document.getElementById(rp_table).rows;
		var invalidRatePeriods = 0;
		for (var i=1; i < rows.length; i++) {
			var row = rows[i];
			var arr = $(row.cells[0]).childElements();
			arr.each(function(node){
				if (node.nodeName == 'INPUT') {
					if (!isTrim(node.value)) {
						invalidRatePeriods++;
						errorCnt++;
						errorIds[errorCnt] = node.id;
					}
				}
		    });
		}		
		if (rows.length < 2) {
			errorMsg += '* There must be at least one rate period.<br />';
		}
		if (invalidRatePeriods > 0) {
			errorMsg += '* Rate period names cannot be blank.<br />';
		}

		// ============================================
		// check to see if rate period prices are valid
		var rp_invalid_price = 0;
		$$('input.rp_price').each(function(s) {
			if (!s.disabled) {
				if (!isTrim(s.value)) {
					rp_invalid_price++;
					errorCnt++;
					errorIds[errorCnt] = s.id;
				}
				if (s.value == null || !isNumeric(s.value)) {
					rp_invalid_price++;
					errorCnt++;
					errorIds[errorCnt] = s.id;
				}		
			}
		});
		if (rp_invalid_price > 0) {
			errorMsg += '* Rate period price cannot be blank and must be a number.<br />';
		}

		if (!checkOverlapDates()) {
			errorMsg += '* Rate period dates cannot overlap.<br />';
		}

	} else {
		var itemBasePrice = $('LoaItemItemBasePrice').getValue();
		if (!isTrim(itemBasePrice) || itemBasePrice == null) {
			$('LoaItemItemBasePrice').value = 0;
		}
		if (!isNumeric(itemBasePrice)) {
			errorMsg += '* Price must be a number.<br />';
			errorCnt++;
			errorIds[errorCnt] = 'LoaItemItemBasePrice';
		}
	}

	if (errorMsg) {
		$('loaItemError').innerHTML = errorMsg;
		Effect.Appear($('loaItemError'), {queue: 'end'});
		for (i=1; i <= errorCnt; i++) {
			$(errorIds[i]).setStyle({ backgroundColor: '#e0c7c7' });
			previousErrorIds[i] = errorIds[i];
		}
		return false;
	} else {
		return true;
	}
}

function checkOverlapDates() {
	// check to see if rate period dates are overlapping
	var rp_invalid_date = 0;
	var start = new Array();
	var end = new Array();
	var c = 0;
	$$('input.rp_date_check').each(function(s) {
		if (!s.disabled) { 
			var rp_split_tmp = (s.id).split('id_rp_date_s_');
			var rp_end_id = 'id_rp_date_e_' + rp_split_tmp[1];
			var d = new Date(s.value);
			start[c] = d.getTime();
			var d = new Date($(rp_end_id).value);
			end[c] = d.getTime();
			c++;
		}
	});
	for (x=0; x<c; x++) {
		for (y=0; y<c; y++) {
			if ((x != y) && ((end[x] > start[y]) && (start[x] < end[y]))) {
				rp_invalid_date = 1;
				y = c;
				x = c;
			}
		}
	}
	if (rp_invalid_date > 0) {
		return false;
	} else {
		return true;
	}
}

function flashError(msg) {
	$('loaItemError').innerHTML = msg;
	Effect.Appear($('loaItemError'), {queue: 'end', duration: 0.5});
	new Effect.Highlight($('loaItemError'), {queue: 'end', duration: 4.0, startcolor: '#ff0000'});
	Effect.Fade($('loaItemError'), {queue: 'end', duration: 1.0});
}

function addDatePicker(id) {
	delete datePickerController.datePickers[id];
	datePickerController.addDatePicker(id,
		{
		'id': id,
		'highlightDays':'0,0,0,0,0,1,1',
		'disableDays':'',
		'divider':'/',
		'format': "m-d-y",
		'locale':true,
		'splitDate':0,
		'noTransparency':true,
		'staticPos':false,
		'hideInput':false,
		}
	);	
}

function setEndDate(did) {
	var id_s = "id_rp_date_s_" + did;
	var id_e = "id_rp_date_e_" + did;
	var ts_s = Date.parse($(id_s).value);
	var ts_e = Date.parse($(id_e).value);

	if (ts_e < ts_s) {
		var new_ts_e = ts_s + (86400000);
		var d = new Date();
		d.setTime(new_ts_e);
		var month = d.getMonth() + 1;
		var day = d.getDate();
		month = PadDigits(month, 2);
		day = PadDigits(day, 2);
		$(id_e).value = month + '/' + day + '/' + d.getFullYear();
	}
}

function getCurrentDate(days) {
	var d = new Date();
	var time_now = d.getTime();
	new_time = time_now + (86400000 * days);
	d.setTime(new_time);

	var month = d.getMonth() + 1;
	var day = d.getDate();
	var year = d.getFullYear();

	month = PadDigits(month, 2);
	day = PadDigits(day, 2);

	var date_string = month + '/' + day + '/' + year;
	return date_string;
}

function PadDigits(n, totalDigits) { 
	n = n.toString(); 
	var pd = ''; 
	if (totalDigits > n.length) 
	{ 
		for (i=0; i < (totalDigits-n.length); i++) 
		{ 
			pd += '0'; 
		} 
	} 
	return pd + n.toString(); 
} 

// For LOA items ADD GROUP 
// ==================================================================
function add_to_group(id) {
	new Effect.Highlight($('pool_' + id), {queue: 'end', duration: 0.5, startcolor: '#ff0000'});
	Effect.Fade($('pool_' + id), {queue: 'end', duration: 0.5});

	Effect.Appear($('group_' + id), {queue: 'end', duration: 0.5});
	new Effect.Highlight($('group_' + id), {queue: 'end', duration: 0.5, startcolor: '#ff0000'});
	
	Field.enable("group_quantity_" + id);
}

function remove_from_group(id) {
	new Effect.Highlight($('group_' + id), {queue: 'end', duration: 0.5, startcolor: '#ff0000'});
	Effect.Fade($('group_' + id), {queue: 'end', duration: 0.5});

	Effect.Appear($('pool_' + id), {queue: 'end', duration: 0.5});
	new Effect.Highlight($('pool_' + id), {queue: 'end', duration: 0.5, startcolor: '#ff0000'});
	
	Field.disable("group_quantity_" + id);
}

// For LOA items list screen (i.e. luxurylink.com/loas/items/5448)
// ==================================================================

function toggleLoaItemRatePeriods(id) {
	var rp_row = 'rp_row_' + id;
	var rp_cell = 'rp_cell_' + id;
	var rp_collapse = 'rp-collapsible-' + id;
	if ($(rp_row).getStyle('display') == 'none') {
		$(rp_collapse).className = 'rp-collapsible';	
		Effect.Appear($(rp_row), {queue: 'end', duration: 0.5});
		$(rp_cell).setStyle({ border: '3px solid #444444' });
	} else {
		$(rp_collapse).className = 'rp-collapsible-closed';	
		Effect.Fade($(rp_row), {queue: 'end', duration: 0.5});
		$(rp_cell).setStyle({ border: '1px solid #c9b865' });
	}
}
