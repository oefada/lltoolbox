<?php
class TrackDetail extends AppModel {

	var $name = 'TrackDetail';
	var $useTable = 'trackDetail';
	var $primaryKey = 'trackDetailId';
	
	var $belongsTo = array('Track' => array('foreignKey' => 'trackId'));
	
	function getTrackRecord($ticketId) {
		$sql = "SELECT t.* FROM ticket tt ";
		$sql.= "INNER JOIN offer o ON o.offerId = tt.offerId ";
		$sql.= "INNER JOIN schedulingInstance si ON si.schedulingInstanceId = o.schedulingInstanceId ";
		$sql.= "INNER JOIN schedulingMaster sm ON sm.schedulingMasterId = si.schedulingMasterId ";
		$sql.= "INNER JOIN schedulingMasterTrackRel smtr ON smtr.schedulingMasterId = sm.schedulingMasterId ";
		$sql.= "INNER JOIN track t ON t.trackId = smtr.trackId ";
		$sql.= "WHERE tt.ticketId = $ticketId";
		$result = $this->query($sql);
		
		if (!empty($result)) {
			$tracks = array();
			foreach ($result as $k => $v) {
				$tracks[] = $v['t'];
			}
			return $tracks;
		} else {
			return false;	
		}
	}
	
	function __getLastTrackDetailRecord($trackId) {
		$last_record = array();
		$result = $this->query("SELECT * FROM trackDetail WHERE trackId = $trackId ORDER BY trackDetailId DESC LIMIT 1");
		if (!empty($result)) {
			$last_record = $result[0]['trackDetail'];
		} else {
			$last_record['cycle'] = 1;
			$last_record['iteration'] = 0;	
			$last_record['keepBalDue'] = 0;
			$last_record['xyRunningTotal'] = 0;
		}
		return $last_record;
	}
	
	function getAllTrackDetails($trackId) {
		$result = $this->query("SELECT * FROM trackDetail WHERE trackId = $trackId ORDER BY trackDetailId DESC");
		if (!empty($result)) {
			return $result;
		} else {
			return false;	
		}
	}
	
	function findExistingTrackTicket($trackId, $ticketId) {
		$result = $this->query("SELECT * FROM trackDetail WHERE trackId = $trackId and ticketId = $ticketId");
		return (!empty($result)) ? true : false;
	}
	
	function getExistingTrackTicket($trackId, $ticketId) {
		$result = $this->query("SELECT * FROM trackDetail WHERE trackId = $trackId and ticketId = $ticketId");
		if (!empty($result)) {
			return $result[0]['trackDetail'];
		} else {
			return false;	
		}
	}
	
	function __getTicketAmount($ticketId, $loaId) {
		$result = $this->query("SELECT t.billingPrice, clpr.*  FROM ticket t INNER JOIN clientLoaPackageRel clpr on clpr.packageId = t.packageId AND clpr.loaId = $loaId WHERE t.ticketId = $ticketId");
		if (!empty($result)) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	function getNewTrackDetailRecord($track, $ticketId) {		
		// set some data vars
		// ---------------------------------------------------------
		$last_track_detail = $this->__getLastTrackDetailRecord($track['trackId']);
		$ticket_and_rev = $this->__getTicketAmount($ticketId, $track['loaId']);
		
		$ticket_amount = $ticket_and_rev['t']['billingPrice'];
		$allocated_amount = (($ticket_and_rev['clpr']['percentOfRevenue'] / 100) * $ticket_amount);
		$result = $this->query("select p.reservePrice from ticket t inner join package p using (packageId) where t.ticketId = $ticketId");
		$reserve_amount = (!empty($result) && isset($result[0]['p']['reservePrice']) && ($result[0]['p']['reservePrice'] > 0)) ? $result[0]['p']['reservePrice'] : false;
		if ($reserve_amount && ($allocated_amount < $reserve_amount)) {
			$allocated_amount = $reserve_amount;
		}

		// set new track information
		// ---------------------------------------------------------
		$new_track_detail 							= array();
		$new_track_detail['trackId']				= $track['trackId'];
		$new_track_detail['ticketId']				= $ticketId;
		$new_track_detail['ticketAmount']			= $ticket_amount;
		$new_track_detail['allocatedAmount']		= $allocated_amount;
		$new_track_detail['initials']				= isset($_SESSION['Auth']['AdminUser']['mailnickname']) ? $_SESSION['Auth']['AdminUser']['mailnickname'] : 'N/A';
		
		$is_y_iteration = false;

		// track detail calculations	
		// ---------------------------------------------------------
		switch ($track['revenueModelId']) {
			case 1:
				// this is a revenue split model
				// ==============================================================
				$new_track_detail['cycle']					= 1;
				$new_track_detail['iteration']				= ++$last_track_detail['iteration'];
				$new_track_detail['amountKept'] 			= ($track['keepPercentage'] / 100) * $allocated_amount;
				$new_track_detail['amountRemitted'] 		= $allocated_amount - $new_track_detail['amountKept'];
				break;
			case 2:
				// this is an x for y AVERAGE
				// ==============================================================
				if (($last_track_detail['iteration'] + 1) == $track['y']) {
					$new_track_detail['cycle']				= $last_track_detail['cycle'];
					$new_track_detail['iteration']			= ++$last_track_detail['iteration'];
					$new_track_detail['xyRunningTotal'] 	= $last_track_detail['xyRunningTotal'] + $allocated_amount;
					$new_track_detail['xyAverage']			= (($new_track_detail['xyRunningTotal'] / $track['y']) * $track['x']);
					$is_y_iteration = true;
					if ($new_track_detail['xyAverage'] > $allocated_amount) {
						$new_track_detail['keepBalDue']		= $new_track_detail['xyAverage'] - $allocated_amount;
						$new_track_detail['amountKept'] 	= $allocated_amount;
						$new_track_detail['amountRemitted'] = 0;
					} else {
						$new_track_detail['keepBalDue']		= 0;
						$new_track_detail['amountKept'] 	= $new_track_detail['xyAverage'];
						$new_track_detail['amountRemitted'] = $allocated_amount - $new_track_detail['amountKept'];
					}
				} else {
					if ($last_track_detail['iteration'] == $track['y']) {
						$new_track_detail['cycle']			= ++$last_track_detail['cycle'];
						$new_track_detail['iteration']		= 1;
						$new_track_detail['xyRunningTotal'] = $allocated_amount;
					} else {
						$new_track_detail['cycle']			= $last_track_detail['cycle'];
						$new_track_detail['iteration']		= ++$last_track_detail['iteration'];
						$new_track_detail['xyRunningTotal'] = $last_track_detail['xyRunningTotal'] + $allocated_amount;
					}
					$new_track_detail['xyAverage']		= 0;
					$new_track_detail['amountKept']		= $last_track_detail['keepBalDue'];
					$new_track_detail['amountRemitted']	= $allocated_amount - $last_track_detail['keepBalDue'];
					$new_track_detail['keepBalDue']		= (($last_track_detail['keepBalDue'] > 0) && ($last_track_detail['keepBalDue'] > $allocated_amount)) ? ($allocated_amount - $last_track_detail['keepBalDue']) : 0;
				}
				break;
			case 3:
				// this is an x for y
				// ==============================================================
				if ($last_track_detail['iteration'] >= $track['y']) {
					$new_track_detail['cycle']			= ++$last_track_detail['cycle'];
					$new_track_detail['iteration']		= 1;
					$new_track_detail['amountKept'] 	= $allocated_amount;
					$new_track_detail['amountRemitted'] = 0;
				} elseif (($last_track_detail['iteration'] + 1) == $track['y']) {
					$new_track_detail['cycle']			= $last_track_detail['cycle'];
					$new_track_detail['iteration']		= ++$last_track_detail['iteration'];
					$new_track_detail['amountKept'] 	= 0;
					$new_track_detail['amountRemitted'] = $allocated_amount;
					$is_y_iteration = true;
				} else {
					$new_track_detail['cycle']			= $last_track_detail['cycle'];
					$new_track_detail['iteration']		= ++$last_track_detail['iteration'];
					$new_track_detail['amountKept'] 	= $allocated_amount;
					$new_track_detail['amountRemitted'] = 0;
				}
				break;
			case 4:
				// this is an x for y with commission
				// ==============================================================
				if ($last_track_detail['iteration'] >= $track['y']) {
					$new_track_detail['cycle']			= ++$last_track_detail['cycle'];
					$new_track_detail['iteration']		= 1;
					$new_track_detail['amountKept'] 	= $allocated_amount;
					$new_track_detail['amountRemitted'] = 0;
				} elseif (($last_track_detail['iteration'] + 1) == $track['y']) {
					$new_track_detail['cycle']			= $last_track_detail['cycle'];
					$new_track_detail['iteration']		= ++$last_track_detail['iteration'];
					$new_track_detail['amountKept'] 	= ($track['commissionPercentage'] / 100) * $allocated_amount;
					$new_track_detail['amountRemitted'] = $allocated_amount - $new_track_detail['amountKept'];
					$is_y_iteration = true;
				} else {
					$new_track_detail['cycle']			= $last_track_detail['cycle'];
					$new_track_detail['iteration']		= ++$last_track_detail['iteration'];
					$new_track_detail['amountKept'] 	= $allocated_amount;
					$new_track_detail['amountRemitted'] = 0;
				}
				break;
			default: 
				// some debug email will go out here
				break;
		}

		// if membership balance met for keep -- disperse allocation to remit and keep
		if ($track['expirationCriteriaId'] == 1 && !(($track['revenueModelId'] == 3 || $track['revenueModelId'] == 4) && $is_y_iteration)) {
			$loa_result = $this->query("SELECT membershipBalance FROM loa WHERE loaId = $track[loaId] LIMIT 1");
			if (!empty($loa_result)) {
				$loa_membership_balance = $loa_result[0]['loa']['membershipBalance'];
				if (($loa_membership_balance - $new_track_detail['amountKept']) < 0) {
					if ($loa_membership_balance > 0) {
						$new_track_detail['amountRemitted'] = $new_track_detail['amountRemitted'] + abs($loa_membership_balance - $new_track_detail['amountKept']);
						$new_track_detail['amountKept'] = $loa_membership_balance;
					} else {
						$new_track_detail['amountRemitted'] = $new_track_detail['amountRemitted'] + $new_track_detail['amountKept'];
						$new_track_detail['amountKept'] = 0;
					}
				}
			} else {
				mail('devmail@luxurylink.com', 'TICKET ALLOCATION: Could not find LOA', print_r($track, true));
			}
		}

		return $new_track_detail;
	}
	
	function reverseBalances($trackDetailId) {
		$errors = 0;
		$trackModel = new Track();
		$loaModel = new Loa();
		$loaModel->recursive = -1;
		
		$trackDetail = $this->read(null, $trackDetailId);
		$allocated_amount = $trackDetail['TrackDetail']['allocatedAmount'];
		$loa = $loaModel->read(null, $trackDetail['Track']['loaId']);

		$track = $trackDetail['Track'];
		
		$track['pending'] += $allocated_amount;
		$track['collected'] -= $allocated_amount;
		$track['modified'] = date('Y-m-d H:i:s', strtotime('now'));
		if (!$trackModel->save($track)) {
			$errors++;
		}
		
		$applyToMembershipBal = ($track['expirationCriteriaId'] == 1) ? true : false;
		$applyToMembershipBal = (($track['revenueModelId'] == 3 || $track['revenueModelId'] == 4) && ($this->data['TrackDetail']['iteration'] == $track['y'])) ? false : $applyToMembershipBal;
		if ($applyToMembershipBal) {
			$loa['Loa']['modified']			  = date('Y-m-d H:i:s', strtotime('now'));
			$loa['Loa']['totalRevenue']			 -= $allocated_amount;
			$loa['Loa']['totalKept']		 -= $trackDetail['TrackDetail']['amountKept'];
			$loa['Loa']['totalRemitted']	 -= $trackDetail['TrackDetail']['amountRemitted'];
			$loa['Loa']['membershipBalance'] += $trackDetail['TrackDetail']['amountKept'];
			if (!$loaModel->save($loa)) {
				$errors++;	
			}
		}
		return (!$errors) ? true : false;
	}
	
	function afterSave() {
		$trackModel = new Track();
		$loaModel = new Loa();
		$loaModel->recursive = -1;
		
		$allocated_amount = $this->data['TrackDetail']['allocatedAmount'];
		$tracks = $this->getTrackRecord($this->data['TrackDetail']['ticketId']);
		$track = $tracks[0];
		
		// retrieve LOA data
		// ---------------------------------------------------------
		$loa = $loaModel->read(null, $track['loaId']);
		
		// update the track record (track)
		// ---------------------------------------------------------
		$track['pending']   -= $allocated_amount;
		$track['collected'] += $allocated_amount;
		$track['modified']	 = date('Y-m-d H:i:s', strtotime('now'));
		$trackModel->save($track);
		
		// update the loa record
		// ---------------------------------------------------------
		$applyToMembershipBal = ($track['expirationCriteriaId'] == 1) ? true : false;
		$applyToMembershipBal = (($track['revenueModelId'] == 3 || $track['revenueModelId'] == 4) && ($this->data['TrackDetail']['iteration'] == $track['y'])) ? false : $applyToMembershipBal;
		if ($applyToMembershipBal) {
			$loa['Loa']['modified']			  = date('Y-m-d H:i:s', strtotime('now'));
			$loa['Loa']['totalRevenue']			 += $allocated_amount;
			$loa['Loa']['totalKept']		 += $this->data['TrackDetail']['amountKept'];
			$loa['Loa']['totalRemitted']	 += $this->data['TrackDetail']['amountRemitted'];
			$loa['Loa']['membershipBalance'] -= $this->data['TrackDetail']['amountKept'];
			$loaModel->save($loa);
		}
		return true;
	}
}
?>
