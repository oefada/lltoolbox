<?php
class ClientSyncShell extends Shell {
	public $uses = array('Client');
	public $logfile = 'client_loa_sync';
	public $errors = array();

    function main() {
    	$this->log('Process Started.', $this->logfile);

		$clients = $this->getActiveLoasForPeriod();
		$this->log('Proceeding to synchronize ' . sizeof($clients) . ' client LOAs.', $this->logfile);

		// iterate through each client
		foreach($clients as $client) {
			// only work with $clients with valid $sites
			if ($this->validSites($client['Client']['sites'])) { 
				$this->log('Synchronizing LOA for clientId: ' . $client['Client']['clientId'], $this->logfile);

				// trigger a Client->save() to update frontend fields
				$this->Client->recursive = 2;
				$this->Client->save($this->Client->read(null, $client['Client']['clientId']));
			}
			else {
				$message = 'Client ID "' . $client['Client']['clientId'] . '" contains an invalid site.';
				$this->log($message, $this->logfile);
				$this->errors[] = $message;
			}
		}
		if (!empty($this->errors)) {
			$this->sendEmailNotification($this->errors);
		}
		$this->log('Process Completed.', $this->logfile);
	}

	function getActiveLoasForPeriod() {
		$currentDate = date('Y-m-d');
		$startDate = $currentDate . ' 00:00:00';
		$endDate = $currentDate . ' 23:59:59';

		$sql = "
			SELECT
				Client.clientId
			FROM
				client Client
			LEFT JOIN loa Loa on Loa.clientId = Client.clientId
			WHERE
				Loa.inactive = 0
				AND (
					((Loa.startDate >= '$startDate' AND Loa.startDate <= '$endDate'))
					OR ((Loa.endDate >= '$startDate' AND Loa.endDate <= '$endDate'))
				)
		";		

		$client_ids = Set::extract('/Client/clientId', $this->Client->query($sql));
		$params = array(
			'recursive' => -1,
			'fields' => array('Client.clientId', 'Client.sites'),
			'conditions' => array(
				'Client.clientId' => $client_ids
			),
		);
		return $this->Client->find('all', $params);
	}

	function validSites($sites) {
		$validSites = array('family', 'luxurylink');

		foreach($sites as $site) {
			if (!in_array($site, $validSites)) {
				return false;
			}
		}
		
		return true;
	}
	
	function sendEmailNotification($messages) {
		$emailTo = 'mclifford@luxurylink.com';
		$emailSubject = "Error encountered in Toolbox process";
		$emailHeaders = "From: LuxuryLink.com DevMail<devmail@luxurylink.com>\r\n";
		$emailBody = "While synchronizing LOA statuses between the backend and frontend databases I encountered the following error(s):\r\n\r\n";
		foreach($messages as $message) {
			$emailBody .= $message . "\r\n";
		}
		@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);		
	}
}
?>