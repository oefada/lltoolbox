<?php
class DealAlertsController extends AppController {

	var $name = 'DealAlerts';
	var $helpers = array('Layout');
	var $components = array('RequestHandler');
	
	function beforeFilter() { $this->LdapAuth->allow('*'); Configure::write('debug', '0'); }
	function email_blast() {
		$date = date('Y-m-d H:i:s');
		
		$emailsToSend = array();
		$this->autoRender = false;

		//TAKE CARE OF THE ORIGINAL SUBSCRIBERS
		//the last action date is the same for all of these, so it's easy to find the new packages for all clients
		//one global date to search for for each client, so we can calculate one global list of new packages per client
		$subs = $this->DealAlert->query("SELECT DealAlert.*, User.userId, User.email, User.firstName, User.lastName FROM dealAlert AS DealAlert
										INNER JOIN user AS User USING(userId)
										WHERE lastAction = 'EMAIL'");

		foreach ($subs as $sub) {
			$clientId = $sub['DealAlert']['clientId'];
			
			//if this client doesn't have a list of new packages, create it now
			if (!isset($newPackages[$clientId])):
				$tmpNew = $this->DealAlert->query("SELECT DISTINCT(OfferLive.packageId), Client.name, OfferLive.shortBlurb,
														Client.clientId,
														oldProductId,
														seoName,
														OfferLive.offerId,
														Client.locationDisplay,
														OfferLive.startDate
														FROM offerLive AS OfferLive 
														INNER JOIN clientLoaPackageRel cl USING(packageId)
														INNER JOIN client AS Client ON(Client.clientId = $clientId)
														LEFT JOIN offerLive AS OfferLivePrev ON (OfferLivePrev.startDate <= '{$sub['DealAlert']['lastActionDate']}' AND OfferLivePrev.packageId = OfferLive.packageId)
														WHERE cl.clientId = $clientId
														AND OfferLivePrev.offerId IS NULL");

				$tmp = array();
				foreach ($tmpNew as $pkg) {
					$tmp[] = array('packageId' => $pkg['OfferLive']['packageId'],
									'clientName' => $pkg['Client']['name'],
									'clientUrl' => "http://www.luxurylink.com/luxury-hotels/".$pkg['Client']['seoName']."?clid=".$pkg['Client']['clientId'],
									'shortBlurb' => $pkg['OfferLive']['shortBlurb'],
									'oldProductId' => $pkg['Client']['oldProductId'],
									'seoName' => $pkg['Client']['seoName'],
									'offerId' => $pkg['OfferLive']['offerId'],
									'clientId' => $pkg['Client']['clientId'],
									'locationDisplay' => $pkg['Client']['locationDisplay'],
									'startDate' => $pkg['OfferLive']['startDate']);
				}
								
				$newPackages[$clientId] = $tmp;
			endif;
			if ($newPackages[$clientId]) {
				@$emailsToSend[$sub['DealAlert']['userId']] = array('email' => $sub['User']['email'],
																	'firstName' => $sub['User']['firstName'],
																	'lastName' => $sub['User']['lastName'],
																	'userId' => $sub['User']['userId'],
																	'packages' => array_merge((array)$emailsToSend[$sub['DealAlert']['userId']]['packages'], (array)$newPackages[$clientId]));
			}
		}
		
		//take care of all new subscribers
		//the dates can be different for all, so must calculate new packages per subscriber
		$subs = $this->DealAlert->query("SELECT DealAlert.*, User.email FROM dealAlert AS DealAlert
										INNER JOIN user AS User USING(userId)
										WHERE lastAction = 'SUBSCRIBE'");		
		foreach ($subs as $sub) {
			$clientId = $sub['DealAlert']['clientId'];
			
			//if this client doesn't have a list of new packages, create it now
			$tmpNew = $this->DealAlert->query("SELECT DISTINCT(OfferLive.packageId), Client.name, OfferLive.shortBlurb,
													Client.clientId,
													oldProductId,
													seoName,
													OfferLive.offerId,
													Client.locationDisplay,
													OfferLive.startDate
														FROM offerLive AS OfferLive 
														INNER JOIN clientLoaPackageRel cl USING(packageId)
														INNER JOIN client AS Client ON(Client.clientId = $clientId)
														LEFT JOIN offerLive AS OfferLivePrev ON (OfferLivePrev.startDate <= '{$sub['DealAlert']['lastActionDate']}' AND OfferLivePrev.packageId = OfferLive.packageId)
														WHERE cl.clientId = $clientId
														AND OfferLivePrev.offerId IS NULL");

				$tmp = array();
				foreach ($tmpNew as $pkg) {
					$tmp[] = array('packageId' => $pkg['OfferLive']['packageId'],
									'clientName' => $pkg['Client']['name'],
									'clientUrl' => "http://www.luxurylink.com/luxury-hotels/".$pkg['Client']['seoName']."?clid=".$pkg['Client']['clientId'],
									'shortBlurb' => $pkg['OfferLive']['shortBlurb'],
									'oldProductId' => $pkg['Client']['oldProductId'],
									'seoName' => $pkg['Client']['seoName'],
									'offerId' => $pkg['OfferLive']['offerId'],
									'clientId' => $pkg['Client']['clientId'],
									'locationDisplay' => $pkg['CLient']['locationDisplay']);
				}
							
			
			$emailsToSend[$sub['DealAlert']['userId']] = array('email' => $sub['User']['email'],
																	'firstName' => $sub['User']['firstName'],
																	'lastName' => $sub['User']['lastName'],
																	'userId' => $sub['User']['userId'],
																	'packages' => array_merge((array)$emailsToSend[$sub['DealAlert']['userId']]['packages'], (array)$tmp));
		}

		$subs = $this->DealAlert->query("UPDATE dealAlert SET lastActionDate = '$date', lastAction = 'EMAIL'");
		
		foreach ($emailsToSend as $k => $v) {
			foreach ($v['packages'] as $k2 => $v2) {
				$this->mail($v['email'], $v['firstName'], $v['lastName'], $v['userId'], $v2);
			}
		}

	}
	
	function mail($email, $firstName, $lastName, $userId, $package) {
		$package['email'] = $email;
		$package['firstName'] = $firstName;
		$package['lastName'] = $lastName;
		$package['userId'] = $userId;
		// fetch template with the vars above
		// -------------------------------------------------------------------------------
		ob_start();
		include(APP_PATH.'vendors/email_msgs/deal_alert.html');
		$emailBody = ob_get_clean();
		
		$emailSubject = $package['clientName']." has a New Package";
		$emailFrom = "Luxurylink.com<no-reply@luxurylink.com>";
		$emailReplyTo = "no-reply@luxurylink.com";
		$emailTo = $email;
		
		// send out ppv and winner notification emails
		// -------------------------------------------------------------------------------
		$emailHeaders = "From: $emailFrom\r\n";
		$emailHeaders.= "Reply-To: $emailReplyTo\r\n";
    	$emailHeaders.= "Content-type: text/html\r\n";
		
		@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);
	}
}
?>