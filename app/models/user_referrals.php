<?php

/*
- if userReferrals.referrerBonusApplied = 0, then apply credit to the referrer. 
	Once applied, set referrerBonusApplied =1 and set statusTypeId = 3
- if userReferrals.referrerBonusApplied = 1, then do not apply credit and set statusTypeId = 3
- if userReferrals.referredBonusApplied = 0, then apply credit to the referred. 
	Once applied, set referredBonusApplied =1 and set statusTypeId = 3

Status
1 No Response
2 Accepted
3 Purchased
4 Already Registered
*/

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

			$arr=array('file' => "appshared".DS."helpers".DS."UserReferralsPartialHelper.php");
			App::import("Vendor","UserReferralsPartialHelper", $arr);


			// give credit to referred user
			if ($referral['UserReferrals']['referredBonusApplied']==0) {
				if (is_array($referredUser) && isset($referredUser['User'])) {
					$creditArr = Array();
					$siteId=$referral['UserReferrals']['siteId'];
					$createdDt=$referral['UserReferrals']['createdDt'];
					$amount=UserReferralsPartialHelper::getRafAmountReceiver($siteId, $createdDt);
					if ($amount>0){
						$creditArr['CreditTracking'] = Array(
							'creditTrackingTypeId' => 3,
							'userId' => $referredUser['User']['userId'],
							'amount' => $amount 
						);
						$this->CreditTracking->create();
						$this->CreditTracking->save($creditArr);

						$params=array('id'=>$id,'referredBonusApplied'=>1, 'statusTypeId'=>3);
						$this->save($params);
					}

				}
			}

			// give credit to referrer
			if ($referral['UserReferrals']['referrerBonusApplied']==0) {
				$creditArr = Array();
				$siteId=$referral['UserReferrals']['siteId'];
				$createdDt=$referral['UserReferrals']['createdDt'];
				$amount=UserReferralsPartialHelper::getRafAmountSender($siteId, $createdDt);

				if ($amount>0){
					$creditArr['CreditTracking'] = Array(
						'creditTrackingTypeId' => 3,
						'userId' => $referral['UserReferrals']['referrerUserId'],
						'amount' =>$amount 
					);
					$this->CreditTracking->create();
					$this->CreditTracking->save($creditArr);
					$params=array('id'=>$id,'referrerBonusApplied'=>1);
					$this->save($params);
				}
			}
			if ($status!=3 && ($referral['UserReferrals']['referredBonusApplied']==1 || 
				$referral['UserReferrals']['referrerBonusApplied']==1
				)){
				$params=array('id'=>$id, 'statusTypeId'=>3);
				$this->save($params);
			}

			return $status;
		}

		return false;
	}
}
