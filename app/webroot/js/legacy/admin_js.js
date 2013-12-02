function popOffers(year, month, day) {

	var pop_link = "PopUpOffer.phtml?";
	pop_link += "year=" + year;
	pop_link += "&month=" + month;
	pop_link += "&day=" + day;
	
	var win = window.open(pop_link,"","statusbar=no,resizable=yes,width=500,height=400");
}

function popDest() {
	var pop_link = "PopUpDest.phtml";
	var dest_ids = '';

	var rows = document.getElementById('dest_table').tBodies[0].rows;
    for (var i=0; i<rows.length; i++) {
		var row = rows[i];
		var cell = row.cells[1];
		if(cell.firstChild.nodeValue > 0) {
			dest_ids += cell.firstChild.nodeValue + '-';
		 }
	}

	pop_link += "?dest_ids=" + dest_ids;

	var win = window.open(pop_link,"","statusbar=no,scrollbars=yes,resizable=yes,width=500,height=700");
}

function updateParent(str1, str2) {
	//window.opener.alert(str1);
	//alert(window.opener.str1.value);
	var parent_win = window.opener.document.getElementById(str1).innerHTML = 'asdgasdgasdg';
	window.close();
}

function processNewOffer(offer_id, year, month, day) {
	var link = "../Product/PopUpOffer.phtml?do_ajax=1&offer_id=" + offer_id;
	link += "&year=" + year;
	link += "&month=" + month;
	link += "&day=" + day;
	executeAjax(link, testFunk);
}

function testFunk() {
	if (xmlHttp.readyState==4){
		document.getElementById('offer_result').innerHTML = xmlHttp.responseText
	}
}

function updateMsg() {
	if (xmlHttp.readyState==4){
		document.getElementById('updateMsg').innerHTML = xmlHttp.responseText
	}
	setTimeout("hideDiv('updateMsg')", 3000)
}

function hideDiv(div_id) {
	document.getElementById(div_id).style.display = 'none';
}

function ajaxLoader() {
	document.getElementById("ajaxLoader").style.display = '';
	document.getElementById("ajaxLoader").innerHTML = '<img src="../Images/ajax-loader2.gif" />';
}

function ajaxLoaderClose() {
	document.getElementById("ajaxLoader").innerHTML = ''
	document.getElementById("ajaxLoader").style.display = 'none';
}

function processUpdate(year, month, day, tbl_id) {
	document.getElementById('submit_but').style.display = 'none';
	ajaxLoader();

	var auction_ids = '';
	var rows = document.getElementById(tbl_id).tBodies[0].rows;
    for (var i=1; i<rows.length; i++) {
	    var row = rows[i];
	    var cell = row.cells[1];
		auction_ids += cell.firstChild.nodeValue + '-';
	}

	var link = "../Product/PromoTool.phtml?do_ajax=1";
	link += "&year=" + year;
	link += "&month=" + month;
	link += "&day=" + day;
	link += "&auction_str=" + auction_ids;
	
	executeAjax(link, updateMsg);	

	document.getElementById('submit_but').style.display = '';
	ajaxLoaderClose();
	document.getElementById('updateMsg').innerHTML = '';
	document.getElementById('updateMsg').style.display = '';;
}

function processDestUpdate(tbl_id) {
	document.getElementById('submit_but').style.display = 'none';
	ajaxLoader();

	var style_ids = '';
	var rows = document.getElementById(tbl_id).tBodies[0].rows;
    for (var i=1; i<rows.length; i++) {
	    var row = rows[i];
	    var cell = row.cells[1];
		style_ids += cell.firstChild.nodeValue + '-';
	}

	var link = "../Destination/DestTool.phtml?do_ajax=1";
	link += "&style_string=" + style_ids;
	
	executeAjax(link, updateMsg);	

	document.getElementById('submit_but').style.display = '';
	ajaxLoaderClose();
	document.getElementById('updateMsg').innerHTML = '';
	document.getElementById('updateMsg').style.display = '';;
}

function appendRow(tblId, offerId, auctionName, d_open, d_close)
{
	var tbl = window.opener.document.getElementById(tblId);
	var newRow = tbl.insertRow(tbl.rows.length);
	var newCell = newRow.insertCell(0);
	
	/*var removeLink = "<a href=\"Javascript:deleteRow('" + tblId + "', parseInt(this.innerHTML))\">";
	removeLink += tbl.rows.length - 1;
	removeLink += "</a>";
	newCell.innerHTML = removeLink;*/

	newCell.innerHTML = tbl.rows.length - 1;
	newCell.setAttribute("onDblClick","deleteRow('" + tblId + "',parseInt(this.innerHTML))");
	//newCell.setAttribute('onClick', "alert('asdfasdf');");
	//newCell.setAttribute("onDblClick",deleteRow(tblId,'parseInt(this.innerHTML)'));
	//newCell.attachEvent("onDblClick","deleteRow('" + tblId + "',parseInt(this.innerHTML))");
	//newCell.attachEvent('onClick', deleteRow);
	//newCell.onClick = function() {alert('asdf')};

	var newCell = newRow.insertCell(1);
	newCell.innerHTML =  offerId ;

	var newCell = newRow.insertCell(2);
	newCell.innerHTML = auctionName
	
	if(tblId == 'dest_table') {
		window.opener.initDrag(tblId);
		return false;
	}

	var newCell = newRow.insertCell(3);
	newCell.innerHTML = d_open;
	
	var newCell = newRow.insertCell(4);
	newCell.innerHTML = d_close;

	window.close();
	window.opener.initDrag();
}

function changeDate() {
	var year = document.getElementById('s_year').value;
	var month = document.getElementById('s_month').value;
	var day = document.getElementById('s_day').value;

	if(year && month && day) {
		window.location.replace('PromoTool.phtml?year=' + year + '&month=' + month + '&day=' + day);
	}
}

function initDrag(tbl_id) {
	if(tbl_id) {
		var table = document.getElementById(tbl_id);
	}else {
		var table = document.getElementById("te_table");
	}
	var tableDnD = new TableDnD();
	tableDnD.init(table);
}

function deleteRow(tblId, txtIndex)
{
	var askThem = confirm("You sure you want to delete Slot # " + txtIndex + "?");
	if(!askThem) {
		return false;
	}

	var tbl = document.getElementById(tblId);
	
	tbl.deleteRow(txtIndex);
	initDrag(tblId);
	reOrderRows(tblId);
}

function resetOffers(year, month, day) {
	var askThem = confirm("You sure you want to reset the offers to the original AUTO-PICKED top escapes?");
	if(!askThem) {
		return false;
	}
	
	var link = "../Product/PromoTool.phtml?do_ajax=1&reset_offers=1";
	link += "&year=" + year
	link += "&month=" + month;
	link += "&day=" + day;

	executeAjax(link, doNothing);
	window.location.replace("../Product/PromoTool.phtml?year="+year+"&month="+month+"&day="+day);
}

function doNothing() {
	//
}

function reOrderRows(tbl_id) {
	var rows = document.getElementById(tbl_id).tBodies[0].rows;
    for (var i=0; i<rows.length; i++) {
		var row = rows[i];
		var cell = row.cells[0];
		if(cell.firstChild.nodeValue > 0) {
			cell.firstChild.nodeValue = i;
		 }
	}
}
