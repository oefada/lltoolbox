<?php

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

class WebServiceUsersController extends WebServicesController
{
	var $name = 'WebServiceUsers';
	var $uses = 'User';
	var $serviceUrl = 'http://192.168.100.22/web_service_users';
	var $errorResponse = array();
	var $api = array(
					'userProcessor1' => array(
						'doc' => 'Any changes to user info from LIVE will be pushed here to update backend',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function userProcessor1($in0)
	{
		$json_decoded = json_decode($in0, true);
		$this->errorResponse = false;
		if (!$this->updateUserBackend($json_decoded)) {
			$json_decoded['response'] = $this->errorResponse;
		} 
		return json_encode($json_decoded);
	}
	
	function updateUserBackend($data) 
	{
		if (!isset($data['User']['userId'])) {
			$this->errorResponse[] = 501;	
			return false;
		}

		$this->User->skipBeforeSaveFilter = true;	

		if ($this->User->saveAll($data, false)) {
			return true;
		} else {
			$this->errorResponse = 505;
			return false;	
		}
		
		/*	
		
		if (isset($data['User']) && !empty($data['User'])) {
			if (!$this->User->save($data['User'])) {
				$this->errorResponse[] = 502;
			}	
		}
		if (isset($data['UserMailOptin']) && !empty($data['UserMailOptin'])) {
			if (!$this->User->UserMailOptin->saveAll($data['UserMailOptin'])) {
				$this->errorResponse[] = 503;
			}
		}
		if (isset($data['UserPaymentSetting']) && !empty($data['UserPaymentSetting'])) {
			if (!$this->User->UserPaymentSetting->saveAll($data['UserPaymentSetting'])) {
				$this->errorResponse[] = 504;	
			}
		}
		if (isset($data['UserPreference']) && !empty($data['UserPreference'])) {
			if (!$this->User->UserPreference->saveAll($data['UserPreference'])) {
				$this->errorResponse[] = 505;
			}
		}
		if (isset($data['UserReferral']) && !empty($data['UserReferral'])) {
			if (!$this->User->UserReferral->saveAll($data['UserReferral'])) {
				$this->errorResponse[] = 506;	
			}
		}
		if (isset($data['UserSiteExtended']) && !empty($data['UserSiteExtended'])) {
			if (!$this->User->UserSiteExtended->saveAll($data['UserSiteExtended'])) {
				$this->errorResponse[] = 507;	
			}
		}
		if (isset($data['Address']) && !empty($data['Address'])) {
			if (!$this->User->Address->saveAll($data['Address'])) {
				$this->errorResponse[] = 508;	
			}	
		}
		
		if (empty($this->errorResponse)) {
			return true;
		} else {
			return false;	
		}
		*/
	}
}
?>