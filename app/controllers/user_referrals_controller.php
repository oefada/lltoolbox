<?php
class UserReferralsController extends AppController {

	var $name = 'UserReferrals';
	var $uses = Array('UserReferrals', 'User', 'CreditTracking');
	
	function completeReferral($id, $status=2, $referrerUserId = null) {
		if (($status = $this->UserReferrals->completeReferral($id, $status)) !== FALSE) {
			$this->Session->setFlash(__('Credit has been applied for user referral', true));
			
			$userId = $this->UserReferrals->read(null, $id);
			
			if ($status == 3) {
				$userId = $userId['UserReferrals']['referrerUserId'];
				$this->redirect(array('controller' => 'users', 'action'=> 'referralssent', 'id' => $userId));
			} else {
				$userId = $userId['User']['userId'];
				$this->redirect(array('controller' => 'users', 'action'=> 'referralsrecvd', 'id' => $userId));
			}
		}
	}
}
?>