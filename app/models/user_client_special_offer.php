<?php
class UserClientSpecialOffer extends AppModel
{

	public $name = 'UserClientSpecialOffer';
	public $useTable = 'userClientSpecialOffers';
	public $primaryKey = 'userClientSpecialOffersId';
	public $belongsTo = array(
			'User' => array('foreignKey' => 'userId'),
			'Client' => array('foreignKey' => 'clientId'),
	);

	function beforeFind($queryData)
	{
		$this->UserClientSpecialOffer->recursive = 2;
		$deleted = array('deleted' => 0);
		if (isset($queryData['conditions'])) {
			if (!isset($queryData['conditions']['deleted'])) {
				$queryData['conditions']['deleted'] = 0;
			}
			$queryData['conditions']['not']['User.email'] = null;
		}
		return $queryData;
	}

	function afterFind($results, $primary)
	{
		foreach ($results as $k => &$v) {
			if (isset($v['UserClientSpecialOffer'])) {
				$v['UserClientSpecialOffer']['leadType'] = 'Special Offer';
			}
		}
		return $results;
	}

}
