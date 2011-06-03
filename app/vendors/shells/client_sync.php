<?php
class ClientSyncShell extends Shell {
	public $uses = array('Client');
	public $logfile = 'client_loa_sync';
	public $numdays = 1;
	public $errors = array();

    function main() {
    	$this->log('Process Started.', $this->logfile);
		
    	$this->numdays = (isset($this->params['n']) AND intval($this->params['n']) !== 0) ? intval($this->params['n']) : 1;
		$clients = $this->getActiveLoasForPeriod($this->numdays);
		$this->log('Proceeding to synchronize ' . sizeof($clients) . ' client LOA\'s.', $this->logfile);

		// iterate through each client
		foreach($clients as $client) {
			// only work with $clients with valid $sites
			if ($this->validSites($client['Client']['sites'])) { 
				$this->log('Synchronizing LOA for clientId: ' . $client['Client']['clientId'], $this->logfile);

				// trigger a Client->save() to update frontend fields
				$this->Client->id = $client['Client']['clientId'];
				$this->Client->save($client);
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

	function getActiveLoasForPeriod($period = 1) {
		// Current date and last $period day(s)
		$current_date = date('Y-m-d H:i:s');
		$date_window = date('Y-m-d H:i:s', strtotime($current_date)-($period*24*60*60));

		$sql = "
			SELECT
				Client.clientId
			FROM
				client Client
			LEFT JOIN loa Loa on Loa.clientId = Client.clientId
			WHERE
				Loa.inactive = 0
				AND (
					((Loa.startDate >= '$date_window' AND Loa.startDate <= '$current_date'))
					OR ((Loa.endDate >= '$date_window' AND Loa.endDate <= '$current_date'))
				)
		";
		
		$client_ids = Set::extract('/Client/clientId', $this->Client->query($sql));
		$params = array(
			'recursive' => 2,
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