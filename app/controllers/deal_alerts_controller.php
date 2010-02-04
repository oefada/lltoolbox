<?php
/**
 * Deal Alerts Class
 *
 * This class takes care of sending user deal alert emails
 */
class DealAlertsController extends AppController {

	var $name = 'DealAlerts';
	var $helpers = array('Layout');
	var $components = array('RequestHandler');
	
	/**
	 * Before filter used to allow access from everyone. Cron job can't log in.
	 */
	function beforeFilter() { $this->LdapAuth->allow('*'); Configure::write('debug', '0'); }
	
	/**
	 * This is the main method in the class. It is called directly from a cron job
	 * It searches available deals for each user registered for deal alert and emails them.
	 */
	function email_blast() {
		$date = date('Y-m-d H:i:s');	//store current date for caching/speed
		
		$emailsToSend = array();		//empty array that will hold the details for all emails to send
		$this->autoRender = false;
		
		//get all subscribers
		$subs = $this->DealAlert->query("SELECT DealAlert.*, User.email, User.userId, User.firstName, User.lastName FROM dealAlert AS DealAlert
										INNER JOIN user AS User USING(userId)");		
		
		/**
		 * For each subscriber, we need to find the deals that pertain to them
		 */
		foreach ($subs as $sub) {
			$clientId = $sub['DealAlert']['clientId'];
			
			//this query just looks in live offer for any new auctions/buy nows
			//the rules state that anything that hasn't been up for the last 30 days is considered 'new'

			switch ($sub['DealAlert']['siteId']) {
				case 1:
					$offerSite = 'offerLuxuryLink';
					$siteUrl = 'http://www.luxurylink.com';
					$siteId = 1;
					break;
				case 2:
					$offerSite = 'offerFamily';
					$siteUrl = 'http://www.familygetaway.com';
					$siteId = 2;
					break;
				default:
					continue;
			}

			$tmpNew = $this->DealAlert->query("SELECT DISTINCT(OfferLive.packageId), Client.name, OfferLive.shortBlurb,
													Client.clientId,
													oldProductId,
													seoName,
					 								Client.locationDisplay
													FROM $offerSite AS OfferLive 
													INNER JOIN clientLoaPackageRel cl USING(packageId)
													INNER JOIN client AS Client ON(Client.clientId = $clientId)
													LEFT JOIN $offerSite AS OfferLivePrev ON (OfferLivePrev.startDate <= ('{$sub['DealAlert']['lastActionDate']}' - INTERVAL 5 MINUTE) 
														AND OfferLivePrev.endDate >= ('{$sub['DealAlert']['lastActionDate']}' - INTERVAL 30 DAY) 
														AND OfferLivePrev.endDate >= '{$sub['DealAlert']['subscribeDate']}' 
														AND OfferLivePrev.packageId = OfferLive.packageId 
														AND OfferLivePrev.isMystery = 0) 
													WHERE cl.clientId = $clientId
														AND OfferLivePrev.offerId IS NULL 
														AND OfferLive.startDate BETWEEN ('{$sub['DealAlert']['lastActionDate']}' - INTERVAL 5 MINUTE) 
														AND '{$sub['DealAlert']['lastActionDate']}' 
														AND OfferLive.isMystery = 0
													GROUP BY OfferLive.packageId");
				
			//create a nice array of all new packages
			$tmp = array();
			foreach ($tmpNew as $pkg) {
				$tmp[] = array('packageId' => $pkg['OfferLive']['packageId'],
								'clientName' => $pkg['Client']['name'],
								'clientUrl' => "$siteUrl/luxury-hotels/".$pkg['Client']['seoName']."?clid=".$pkg['Client']['clientId'],
								'shortBlurb' => $pkg['OfferLive']['shortBlurb'],
								'oldProductId' => $pkg['Client']['oldProductId'],
								'seoName' => $pkg['Client']['seoName'],
								'clientId' => $pkg['Client']['clientId'],
								'siteId' => $siteId, 
								'locationDisplay' => $pkg['CLient']['locationDisplay']);
			}
							
			//if any new packages were found, it means we need to send the user an email
			//store all of this in an easy array
			if (!empty($tmp)) {
				$emailsToSend[$sub['DealAlert']['userId']] = array('email' => $sub['User']['email'],
																	'firstName' => $sub['User']['firstName'],
																	'lastName' => $sub['User']['lastName'],
																	'userId' => $sub['User']['userId'],
																	'packages' => array_merge((array)$emailsToSend[$sub['DealAlert']['userId']]['packages'], (array)$tmp));
			}
		}
		
		$subs = $this->DealAlert->query("UPDATE dealAlert SET lastActionDate = '$date', lastAction = 'EMAIL'");

		//loop through all users and for each new package we send them an email with package details
		foreach ($emailsToSend as $k => $v) {
			foreach ($v['packages'] as $k2 => $v2) {
				$this->mail($v['email'], $v['firstName'], $v['lastName'], $v['userId'], $v2);
			}
		}

	}
	
	/**
	 * Mail helper method. It loads a template file and sends an email with the package details
	 *
	 * @param string $email the email of the user
	 * @param string $firstName
	 * @param string $lastName
	 * @param int $userId
	 * @param array $package the details of the package
	 */
	function mail($email, $firstName, $lastName, $userId, $package) {
		$package['email'] = $email;

		//some users don't have a first or last name, if they don't we greet them with their email
		if (!$firstName && !$lastName) {
			$package['firstName'] = $email;
			$package['lastName'] = '';
		} else {
			$package['firstName'] = $firstName;
			$package['lastName'] = $lastName;
		}
		
		switch ($package['siteId']) {
			case 1:
				$template = 'deal_alert.html';
				$emailFrom = "Luxurylink.com<no-reply@luxurylink.com>";
				$emailReplyTo = "no-reply@luxurylink.com";

				$siteName = 'Luxury Link';
				$siteDisplay = 'LuxuryLink.com';
				$siteEmail = 'luxurylink.com';
				$siteUrl = 'http://www.luxurylink.com/';
				$siteHeader = '990000';
				$sitePhone  = '(888) 297-3299';
				$headerLogo = 'http://www.luxurylink.com/images/ll_logo_2009_2.gif';
				break;
			case 2:
				$template = 'deal_alert_family.html';
				$emailFrom = "FamilyGetaway.com<no-reply@familygetaway.com>";
				$emailReplyTo = "alerts@familygetaway.com";
				
				$siteName = 'Family Getaway';
				$siteDisplay = 'FamilyGetaway.com';
				$siteEmail = 'familygetaway.com';
				$siteUrl = 'http://www.familygetaway.com/';
				$siteHeader = 'DE6F0A';
				$sitePhone  = '(877) 372-5877';
				$headerLogo = 'http://www.luxurylink.com/images/family/logo.gif';
				break;
			default:
				return false;
		}

		$package['userId'] = $userId;
		// fetch template with the vars above
		// -------------------------------------------------------------------------------
		ob_start();
		include(APP_PATH . "vendors/email_msgs/$template");
		$emailBody = ob_get_clean();								//output buffering trick
		
		
		//setup and send the email...
		$emailSubject = $package['clientName']." has a New Package";
		$emailTo = $email;
		
		$emailHeaders = "From: $emailFrom\r\n";
		$emailHeaders.= "Reply-To: $emailReplyTo\r\n";
    	$emailHeaders.= "Content-type: text/html\r\n";
		
		@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);
		@mail('alee@luxurylink.com', $emailSubject, $emailBody, $emailHeaders); // TODO REMOVE ONLY A TEST FOR DEBUG
	}
}
?>
