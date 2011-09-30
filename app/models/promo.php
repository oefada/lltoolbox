<?php
class Promo extends AppModel {

	var $name = 'Promo';
	var $useTable = 'promo';
	var $primaryKey = 'promoId';
	var $displayField = 'promoName';

	var $rafPromoId = 60;

	var $hasAndBelongsToMany = array(
		'PromoCode' =>
		   array('className'    => 'PromoCode',
				 'foreignKey'   => 'promoId',
				 'associationForeignKey'=> 'promoCodeId',
				 'with' => 'promoCodeRel',
				 'unique'       => true,
		   )
	);

	var $hasMany = array('PromoRestrictionClient' => array('foreignKey' => 'promoId'),
						'PromoRestrictionClientType' => array('foreignKey' => 'promoId'),
						'PromoRestrictionDestination' => array('foreignKey' => 'promoId'),
                        'PromoRestrictionTheme' => array('foreignKey' => 'promoId')
					   );

	function checkInsertRaf($promoCodeId) {
		if (!$promoCodeId) {
			return false;
		}

		$sql = "SELECT pco.userId FROM promoCodeOwner pco ";
		$sql.= "INNER JOIN promoCodeRel pcr ON pco.promoCodeId = pcr.promoCodeId AND pcr.promoId = " . $this->rafPromoId;
		$sql.= "	WHERE pco.promoCodeId = $promoCodeId";
		$result = $this->query($sql);

		return (!empty($result)) ? $result[0]['pco']['userId'] : false;
	}

	function getRafData($promoCodeId) {
		if (!$promoCodeId) {
			return false;
		}

		$sql = "SELECT User.firstName, User.email, Charity.charityName FROM promoCodeOwner as PromoCodeOwner ";
		$sql.= "INNER JOIN user as User USING (userId) ";
		$sql.= "LEFT JOIN charity as Charity USING (charityId) ";
		$sql.= "WHERE PromoCodeOwner.promoCodeId = $promoCodeId";
		$result = $this->query($sql);

		return (!empty($result)) ? $result[0] : false;
	}

	function getRafDataByTicketId($ticketId) {
		if (!$ticketId) {
			return false;
		}

		$sql = "SELECT User.firstName, User.email, Charity.charityName FROM ticketReferFriend as TicketReferFriend ";
		$sql.= "INNER JOIN promoCodeOwner as PromoCodeOwner ON TicketReferFriend.referrerUserId = PromoCodeOwner.userId ";
		$sql.= "INNER JOIN user as User ON PromoCodeOwner.userId = User.userId ";
		$sql.= "LEFT JOIN charity as Charity ON PromoCodeOwner.charityId = Charity.charityId ";
		$sql.= "WHERE TicketReferFriend.ticketId = $ticketId";
		$result = $this->query($sql);

		return (!empty($result)) ? $result[0] : false;
	}

	function validatePromoForm($data, $new = true) {
		$errors = array();
		if ($data['promoName'] == '') { $errors[] = 'please complete Promo Name'; }
		if ($data['promoCategoryTypeId'] == '') { $errors[] = 'please select Category'; }
		if ($data['percentOrDollar'] == '') { $errors[] = 'please select either $ or % off'; }
		if ($new) {
			if ($data['promoCode'] == '') {
				if ($data['generatePrefix'] == '' || intval($data['generateQuantity']) == 0) {
					$errors[] = 'please complete either Promo Code OR Prefix and Quantity to generate';
				}
			} else {
				if ($data['generatePrefix'] != '' || $data['generateQuantity'] != '') {
					$errors[] = 'please complete ONLY Promo Code OR Prefix and Quantity to generate';
				}
				if ($this->PromoCode->checkDuplicatePromoCode($data['promoCode'])) {
					$errors[] = 'the Promo Code ' . $data['promoCode'] . ' already exists in the database';
				}
			}
		}
		if (!is_numeric($data['amount'])) { $errors[] = 'Amount must be a number'; }
		if (!is_numeric($data['minPurchaseAmount'])) { $errors[] = 'Minimum Purchase Amount must be a number'; }
		if ($data['percentOrDollar'] == 'D') {
			if ($data['amount'] > intval($data['minPurchaseAmount'])) {
				$errors[] = 'for $ off promos, Minimum Purchase Amount must exceed promo Amount';
			}
		} elseif ($data['percentOrDollar'] == 'P') {
			if ($data['amount'] > 90) {
				$errors[] = 'Amount can not exceed 90%';
			}
		}
		if ($data['startDate'] == '') { $errors[] = 'please complete Start Date'; }
		if ($data['endDate'] == '') { $errors[] = 'please complete End Date'; }
		return $errors;
	}

	function formatPromoFormData($data) {
		$rtn = array();
		if ($data['percentOrDollar'] == 'D') {
			$data['amountOff'] = $data['amount'];
			$data['percentOff'] = 0;
		} elseif ($data['percentOrDollar'] == 'P') {
			$data['amountOff'] = 0;
			$data['percentOff'] = $data['amount'];
		}
		foreach ($data['restrictDestination'] as $k=>$v) {
			if ($v == 0) {
				unset($data['restrictDestination'][$k]);
			}
		}
		foreach ($data['restrictTheme'] as $k=>$v) {
			if ($v == 0) {
				unset($data['restrictTheme'][$k]);
			}
		}
		foreach ($data['restrictClientType'] as $k=>$v) {
			if ($v == 0) {
				unset($data['restrictClientType'][$k]);
			}
		}

		$data['restrictClient'] = ($data['listRestrictedClients'] != '') ? array_unique(explode(',', $data['listRestrictedClients'])) : array();
		foreach ($data['restrictClient'] as $k=>$v) {
			if (intval($v) == 0) {
				unset($data['restrictClient'][$k]);
			}
		}
		$data['listRestrictedClients'] = implode(',', $data['restrictClient']);

		$rtn['Promo'] = $data;

		$rtn['PromoRestrictionDestination'] = array();
		if (is_array($data['restrictDestination'])) {
			foreach ($data['restrictDestination'] as $r) {
				$rtn['PromoRestrictionDestination'][] = array('destinationId'=>intval($r));
			}
		}

		$rtn['PromoRestrictionTheme'] = array();
		if (is_array($data['restrictTheme'])) {
			foreach ($data['restrictTheme'] as $r) {
				$rtn['PromoRestrictionTheme'][] = array('themeId'=>intval($r));
			}
		}

		$rtn['PromoRestrictionClientType'] = array();
		if (is_array($data['restrictClientType'])) {
			foreach ($data['restrictClientType'] as $r) {
				$rtn['PromoRestrictionClientType'][] = array('clientTypeId'=>intval($r));
			}
		}

		$rtn['PromoRestrictionClient'] = array();
		if (is_array($data['restrictClient'])) {
			foreach ($data['restrictClient'] as $r) {
				$rtn['PromoRestrictionClient'][] = array('clientId'=>intval($r));
			}
		}

		return $rtn;
	}

	function setupPromoFormData($id) {
		$rtn = array();
		$this->recursive = 1;
		$promo = $this->read(null, $id);

		$rtn = $promo['Promo'];
		if ($promo['Promo']['amountOff'] > 0) {
			$rtn['percentOrDollar'] = 'D';
			$rtn['amount'] = $promo['Promo']['amountOff'];
		} else {
			$rtn['percentOrDollar'] = 'P';
			$rtn['amount'] = $promo['Promo']['percentOff'];
		}

		$rtn['restrictDestination'] = array();
		foreach ($promo['PromoRestrictionDestination'] as $r) {
			$rtn['restrictDestination'][$r['destinationId']] = $r['destinationId'];
		}

		$rtn['restrictTheme'] = array();
		foreach ($promo['PromoRestrictionTheme'] as $r) {
			$rtn['restrictTheme'][$r['themeId']] = $r['themeId'];
		}

		$rtn['restrictClientType'] = array();
		foreach ($promo['PromoRestrictionClientType'] as $r) {
			$rtn['restrictClientType'][$r['clientTypeId']] = $r['clientTypeId'];
		}

		$rtn['restrictClient'] = array();
		foreach ($promo['PromoRestrictionClient'] as $r) {
			$rtn['restrictClient'][] = $r['clientId'];
		}

		return $rtn;
	}

	function getClientListByIdArray($arr) {
		$displayClients = array();
		if (is_array($arr) && sizeof($arr) > 0) {
			$result = $this->query("
				SELECT clientId, name
				FROM client Client
				WHERE clientId IN (" . implode(',', $arr) . ")
				ORDER BY name"
			);
			foreach ($result as $r) {
			    $displayClients[$r['Client']['clientId']] = $r['Client']['name'];
			}
		}
		return $displayClients;
	}

	function prepDestinationDisplay($destinations, $llInfo = array(), $fgInfo = array()) {
		$rtn = array();
		foreach ($destinations as $d) {
			$sites = array();
			if (array_key_exists($d['Destination']['destinationId'], $llInfo)) { $sites[] = 'LL'; }
			if (array_key_exists($d['Destination']['destinationId'], $fgInfo)) { $sites[] = 'FG'; }
		    $d['Destination']['destinationName'] = $d['Destination']['destinationName'] . '  (' . implode(',', $sites) . ')';
		    $rtn[] = $d;
		}
		return $rtn;
	}

	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$parameters = compact('conditions');
		$this->recursive = $recursive;

		$count = $this->find('count', array_merge($parameters, $extra));

		if (isset($extra['group'])) {
		   $count = $this->getAffectedRows();
		}

		return $count;
	}



}
?>
