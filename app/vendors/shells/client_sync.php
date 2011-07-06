<?php
class ClientSyncShell extends Shell {
	public $uses = array('Client');
	public $logfile = 'client_loa_sync';
	public $errors = array();

    function main() {
		// Set debug to 0 and log to false to suppress logging
		// to app/tmp/error.log as that file was growing quickly
		// and it being too large would cause this script to fail
		// mc 2011-06-22
		Configure::write('debug', 0);
		Configure::write('log', false);
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
		$today = date('Y-m-d');
		$yesterday = date('Y-m-d', strtotime('yesterday'));

		$sql = "
			SELECT
				DISTINCT Client.clientId
			FROM
				client Client
			LEFT JOIN loa Loa on Loa.clientId = Client.clientId
			WHERE Loa.inactive = 0 AND ";

		if (isset($this->params['client_ids'])) {
			$sql .= "Client.clientId in ({$this->params['client_ids']})";
		} else {
			$sql .= "((Loa.startDate BETWEEN '$today 00:00:00' AND '$today 23:59:59') OR (Loa.endDate BETWEEN '$yesterday 00:00:00' AND '$yesterday 23:59:59'))";
		}

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
