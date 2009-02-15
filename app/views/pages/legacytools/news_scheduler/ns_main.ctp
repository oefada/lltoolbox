<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('default_mysql');

ini_set('error_reporting', 2039);

function remove_slashes(&$array) {
	foreach($array as $key=>$val) {
		$array[$key] = stripslashes($val);
	}
}


/* temp connection to luxurymasterDEV */
//mssql_select_db('luxurymasterDEV');


function html_options($array, $selected=null) {
	foreach($array as $val=>$label) {
		$output .= "<option value=\"$val\"" . (($selected == $val) ? ' selected=selected' : '') . ">$label</option>\n";
	}
	return $output;
}

function html_options_numeric($startNum, $selected) {
	for($i=1;$i<=$startNum; $i++) {
		$output .= "<option value=\"$i\"" . (($selected == $i) ? ' selected=selected' : '') . ">$i</option>\n";
	}
	return $output;	
}

function strip_slashes(&$array) {
	foreach($array as $key=>$val) {
		$array[$key] = stripslashes($val);
	}
}
function get_times_of_day($numHours=24, $startTime=0) {
	for($i=$startTime; $i<$startTime+$numHours; $i++) {
		$hour = date('g:ia', mktime($i,0));
		$halfHour = date('g:ia', mktime($i,30));
		$array[$hour] = $hour;
		$array[$halfHour] = $halfHour;
	}
	return $array;
}

function display_segment_product_data($products) {
	if(!count($products)) {
		return;
	}
	$output = '';
	foreach($products as $clientId=>$row) {

		$output .= substr($row['product_name'], 0, 16) . ' (' . $clientId . ')<br>';
	}
	return $output;
}

function display_segment_client_data($clients) {
	if(!count($clients)) {
		return;
	}
	$output = '';
	foreach($clients as $clientId=>$row) {
		$output .= substr($row['client_name'], 0, 16) . '<br>';
	}
	return $output;
}

function return_auc_type_as_str($auction_type_id) {
	/*
	takes aution_type_id and returns
	an array.
	[0] = auction type ex dutch
	[1] = auction type description
	*/
	switch ($auction_type_id) {
		case 1:
			$auction_type_string = 'ST';
			break;
		case 2:
			$auction_type_string = 'BS';
			break;
		case 3: // fixed price
			$auction_type_string = 'EXL';
			break;
		case 4: // fixed price
			$auction_type_string = 'BB';
			break;
		case 6:
			$auction_type_string = 'DU';
			break;
		default:
			return false;
			break;
	}
	return $auction_type_string;
}

if($_POST) {
	remove_slashes($_POST);
}
if($_GET) {
	remove_slashes($_POST);
}

function llsp_del_mailing_segment_offer($params, $offer_id = null) {
	if(is_array($params)) {
		extract($params);
	} else {
		$mailing_segment_id = $params;
	}
	if($offer_id == null) {
		mysql_query("DELETE FROM mailing_segment_offer
		WHERE mailing_segment_id = '$mailing_segment_id'");
	} else {
		mysql_query("DELETE FROM mailing_segment_offer
		WHERE mailing_segment_id = '$mailing_segment_id' AND offer_id = '$offer_id'");
	}
}

function llsp_del_mailing_segment($input) {
	extract($input);
	llsp_del_mailing_segment_offer(array('mailing_segment_id' => $mailing_segment_id));
	llsp_del_mailing_segment_product(array('mailing_segment_id' => $mailing_segment_id));
	llsp_del_mailing_segment_html(array('mailing_segment_id' => $mailing_segment_id));

	return mysql_query("DELETE FROM mailing_segment WHERE mailing_segment_id = '$mailing_segment_id'");
}

function llsp_del_mailing_segment_html($input) {
	extract($input);
	mysql_query("DELETE FROM mailing_segment_html
	WHERE     (mailing_segment_id = '$mailing_segment_id')");
}

function llsp_ins_mailing_segment_product($input) {
	extract($input);
	mysql_query("INSERT INTO mailing_segment_product(mailing_segment_id,clientId)
	VALUES ('$mailing_segment_id', '$clientId')");
	echo mysql_error();
}

function llsp_ins_mailing_segment_credit($input) {
	extract($input);
	mysql_query("DELETE FROM mailing_segment_credit
	WHERE mailing_segment_id = '$mailing_schedule_id'
	AND LOA_id = '$LOA_id'");

	mysql_query("INSERT INTO mailing_segment_credit (mailing_segment_id, LOA_id, mailing_segment_credit_type_id)
	VALUES ('$mailing_schedule_id', '$LOA_id', '$mailing_segment_credit_type_id')");
}

function llsp_ins_mailing_segment_offer($input) {
	extract($input);
	mysql_query("INSERT INTO mailing_segment_offer(mailing_segment_id,offer_id)
	VALUES ('$mailing_segment_id', '$offer_id')");
}

function llsp_upd_mailing_segment($params) {
	extract($params);
	mysql_query("UPDATE mailing_segment
	SET mailing_segment_position_id='$mailing_segment_position_id',mailing_segment_type_id='$mailing_segment_type_id',
	mailing_segment_sort_order='$mailing_segment_sort_order'
	WHERE mailing_segment_id='$mailing_segment_id'");

	if($mailing_segment_type_id == 1 || $mailing_segment_type_id == 3) {
		llsp_del_mailing_segment_offer($mailing_segment_id);
	}
	
	if($mailing_segment_type_id == 4) {
		llsp_del_mailing_segment_offer($mailing_segment_id);
		llsp_del_mailing_segment_product($mailing_segment_id);
	}
}

function llsp_del_mailing_segment_product($input) {
	extract($input);
	mysql_query("DELETE FROM mailing_segment_credit WHERE mailing_segment_id = '$mailing_segment_id'");

	if (empty($clientId)) {
		mysql_query("DELETE FROM mailing_segment_product
		WHERE mailing_segment_id = '$mailing_segment_id'");
	} else {
		mysql_query("DELETE FROM mailing_segment_product
		WHERE mailing_segment_id = '$mailing_segment_id' AND clientId = '$clientId'");
	}

}

?>
<script language="javascript" src="/js/news_scheduler/date_picker.js"></script>
<script language="javascript" src="/js/news_scheduler/js_general.js"></script>

<a href="ns_main?pg=ns_listing">Mailing Calendar</a> | <a href="ns_client_schedule">Client Mailing Schedule</a>
<br>
<br>
<br>

<?
$pg = ($_GET['pg']) ? $_GET['pg'] : $_POST['pg'];
if($pg) {
	include($pg . '.ctp');
}
?>