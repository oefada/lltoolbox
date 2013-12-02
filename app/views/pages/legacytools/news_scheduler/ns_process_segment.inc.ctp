<?php

	// This file is included by ns_product_listing and ns_segment_maint in POST branch of logic
	strip_slashes($_POST);
	// Set some global variables used for sproc params - used in other parts of page also I think
	$mailing_schedule_id = $_POST['mailing_schedule_id'];
	$mailing_segment_sort_order = ($_POST['mailing_segment_sort_order']) ? $_POST['mailing_segment_sort_order'] : 0;
	$offer_id = ($_POST['offer_id']) ? $_POST['offer_id'] : 'NULL';
	$clientId = ($_POST['clientId']) ? $_POST['clientId'] : 'NULL';
	$mailing_schedule_date = ($_GET['mailing_date']);
	$params = array(
					'mailing_schedule_id'=>$mailing_schedule_id,
					'mailing_segment_position_id'=>$_POST['mailing_segment_position_id'],
					'mailing_segment_type_id'=>$_POST['mailing_segment_type_id'],
					'mailing_segment_sort_order'=>$mailing_segment_sort_order
				);
	//Grab mailing date to determine LOA later TW 12.19.07 
	$result_timestamp = mysql_query("SELECT mailing_timestamp FROM mailing_schedule
	WHERE mailing_schedule_id = '$mailing_schedule_id'");
	$mailing_timestamp1 = mysql_result($result_timestamp, 0, 'mailing_timestamp');
	$mailing_schedule_date = date ('Y-m-d',$mailing_timestamp1);

	// Update segment
	if($_POST['mailing_segment_id']) { 
		$mailing_segment_id = $_POST['mailing_segment_id'];
		// Remove mailing_schedule_id from $params - not needed for update now that we have a segment_id.
		array_shift($params);
		$params = array_merge(array('mailing_segment_id'=>$_POST['mailing_segment_id']), $params);
		// update segment sproc need from paul
		$result = llsp_upd_mailing_segment($params);
	}else{ // Create segment
		extract($params);
		$result = mysql_query("INSERT INTO mailing_segment
		                      (mailing_schedule_id, mailing_segment_position_id, mailing_segment_type_id,mailing_segment_sort_order)
		VALUES     ('$mailing_schedule_id', '$mailing_segment_position_id', '$mailing_segment_type_id', '$mailing_segment_sort_order')");

		$mailing_segment_id = mysql_insert_id();
	}

	if($_POST['offer_id'] || $_POST['clientId']) {

		
		$result = mysql_query('SELECT Loa.loaId AS loaId FROM loa AS Loa
								INNER JOIN client as Client ON (Client.clientId = Loa.clientId) 
								WHERE Client.clientId = "'.$_POST['clientId'].'" AND "'.$mailing_schedule_date.'" BETWEEN Loa.startDate AND Loa.endDate');
								


		$loa_id = mysql_result($result, 0, 'loaId');

		if(!$loa_id) {
			die('Failed to retrieve LOA. Please contact Tech Dept!');
		}

		mysql_query("DELETE FROM mailing_segment_offer WHERE mailing_segment_id = '$mailing_segment_id'");
		mysql_query("DELETE FROM mailing_segment_product WHERE mailing_segment_id = '$mailing_segment_id'");
		
		
		$offer_id = $_POST['offer_id'];
		$clientId = $_POST['clientId'];
		
		mysql_query("SELECT * FROM package WHERE packageId = '$offer_id'");
		$offer_exists = mysql_affected_rows();

		mysql_query("SELECT * FROM client WHERE clientId = '$clientId'");
		$product_exists = mysql_affected_rows();

		mysql_query("SELECT schedulingInstance.schedulingInstanceId,clientLoaPackageRel.clientId FROM schedulingInstance
					INNER JOIN schedulingMaster ON(schedulingInstance.schedulingMasterId = schedulingMaster.schedulingMasterId)
					INNER JOIN package ON(schedulingMaster.packageId = package.packageId)
					INNER JOIN clientLoaPackageRel ON(package.packageId = clientLoaPackageRel.packageId)
					WHERE package.packageId = '$offer_id' AND clientLoaPackageRel.clientId = '$clientId'");
		$product_offer_match = mysql_affected_rows();

		$validate_bitmask = base_convert($product_offer_match . $product_exists . $offer_exists, 2, 10);

	}
	// Insert offers and products if validated

	if(($_POST['mailing_segment_type_id'] == 1 || $_POST['mailing_segment_type_id'] == 3) && $_POST['clientId'] && $validate_bitmask == 2) {
		llsp_ins_mailing_segment_product(array('mailing_segment_id'=>$mailing_segment_id, 'clientId'=>$_POST['clientId']));
		llsp_ins_mailing_segment_credit(array('mailing_schedule_id'=>$mailing_segment_id, 'LOA_id'=>$loa_id, 'mailing_segment_credit_type_id'=>$_POST['mailing_segment_credit_type_id']));
	}
	if($_POST['mailing_segment_type_id'] == 2 && $_POST['offer_id'] && $validate_bitmask == 7) {
		llsp_ins_mailing_segment_product(array('mailing_segment_id'=>$mailing_segment_id, 'clientId'=>$_POST['clientId']));
		llsp_ins_mailing_segment_offer(array('mailing_segment_id'=>$mailing_segment_id, 'offer_id'=>$_POST['offer_id']));
		llsp_ins_mailing_segment_credit(array('mailing_schedule_id'=>$mailing_segment_id, 'LOA_id'=>$loa_id, 'mailing_segment_credit_type_id'=>$_POST['mailing_segment_credit_type_id']));
	}

	// HTML
	if($_POST['mailing_segment_html']) {
		$mailing_segment_html = $_POST['mailing_segment_html'];
		
		mysql_query("SELECT mailing_segment_id FROM mailing_segment_html WHERE mailing_segment_id = '$mailing_segment_id'");
		if (mysql_affected_rows()) {
			mysql_query("UPDATE    mailing_segment_html
			SET              mailing_segment_html = '$mailing_segment_html'
			WHERE     (mailing_segment_id = '$mailing_segment_id')");			
		} else {
			mysql_query("INSERT INTO mailing_segment_html (mailing_segment_id,mailing_segment_html)
			VALUES     ('$mailing_segment_id', '$mailing_segment_html')");
		}
	}else{
		llsp_del_mailing_segment_html(array("mailing_segment_id" => $mailing_segment_id));
	}


?>