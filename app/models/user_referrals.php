<?php
class UserReferrals extends AppModel {

	var $name = 'UserReferrals';
	var $useTable = 'userReferrals';
	var $primaryKey = 'id';

	var $actsAs = array('Transactional');
	 	
	var $belongsTo = Array('User' => Array('foreignKey' => 'referrerUserId'));
	
	public function completeReferral($id, $status = 2)
	{
		if ($id && ($status == 2 || $status == 3)) {
			// update statusTypeId
			$referral = $this->read(null, $id);
			$this->set('statusTypeId', $status);
			$this->save();
			
			// give credit to referred email
			$params = Array(
				'conditions' => Array('email' => $referral['UserReferrals']['referredEmail']),
				'recursive' => 0
			);
			
			$referredUser = $this->User->find('first', $params);
			$this->CreditTracking = ClassRegistry::init("CreditTracking");
			
			if (is_array($referredUser) && isset($referredUser['User'])) {
				$creditArr = Array();
				$creditArr['CreditTracking'] = Array(
					'creditTrackingTypeId' => 3,
					'userId' => $referredUser['User']['userId'],
					'amount' => 100
				);
				$this->CreditTracking->create();
				$this->CreditTracking->save($creditArr);
			}

			// give credit to referrer
			if ($status == 3) {
				$creditArr = Array();
				$creditArr['CreditTracking'] = Array(
					'creditTrackingTypeId' => 3,
					'userId' => $referral['UserReferrals']['referrerUserId'],
					'amount' => 100
				);
				$this->CreditTracking->create();
				$this->CreditTracking->save($creditArr);
			}
				
			return $status;
		}

		return false;
	}
}