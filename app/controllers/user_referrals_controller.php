<?php
class UserReferralsController extends AppController {

	var $name = 'UserReferrals';
	var $uses = Array('UserReferrals', 'User', 'CreditTracking');
	
	function completeReferral($id, $status=2) {
		if (!$id) {
		
		} else if ($status == 2 || $status == 3) {
			
			// update statusTypeId
			$referral = $this->UserReferrals->read(null, $id);
			$this->UserReferrals->set('statusTypeId', $status);
			$this->UserReferrals->save();
			$this->Session->setFlash(__('Credit has been applied for user referral', true));
			
			// give credit to referred email
			$params = Array(
				'conditions' => Array('email' => $referral['UserReferrals']['referredEmail']),
				'recursive' => 0
			);
			$referredUser = $this->User->find('first', $params);
			
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
				
			// give credit to referred email
			
			if ($status == 3) {
				$this->redirect(array('controller' => 'users', 'action'=> 'referralssent', 'id' => $referral['UserReferrals']['referrerUserId']));
			} else {
				$this->redirect(array('controller' => 'users', 'action'=> 'referralsrecvd', 'id' => $referredUser['User']['userId']));
			}
			
		}
	}
}
?>