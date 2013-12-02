<?php
App::Import('Model', 'Client');
class ClientNewsletterNotifier extends AppModel {

	var $name = 'ClientNewsletterNotifier';
	var $useTable = false;

	function prepareContactDetails() {
		$site = $this->data[$this->name]['site'];
		$url = $this->data[$this->name]['url'];

		$handle = fopen($url, "r");
		$contents = '';
		while (!feof($handle)) {
		  $contents .= fread($handle, 8192);
		}
		fclose($handle);

		//$clients = $this->getClientsFromHtml($contents, $site);
		$clients = $this->getClientsFromNewsletter($contents, $site);

		// $this->Client = ClassRegistry::init("Client");
		$this->Client = new Client;

		$cl = $this->Client->find('all', array(
										'fields' => array('name', 'managerUsername'),
										'conditions' => array('Client.clientId' => $clients),
										'contain' => array('ClientContact' => array('conditions' => array('ClientContact.clientContactTypeId' => 2), 'order' => 'ClientContact.primaryContact DESC')),
										));

		//weed out the non-sponsorship clients
		$sponsorshipClients = array();
		foreach ($cl as $client) {
			if ($client['ClientLevel']['clientLevelId'] == 2) {
				$sponsorshipClients[] = $client;
			}
		}

		return $sponsorshipClients;
	}


	function getClientsFromNewsletter($data, $site) {

		preg_match("~<input(.*?)name='clientIds' value='([^']+)'~is",$data,$arr);
		$clientIdArr=explode(",",$arr[2]);

		return $clientIdArr;
/*
		$match_str = '';

		// Right now the $match_str looks similar for both luxurylink and familygetaway (only the URL is different)
		// This may not be the case long term so they are broken out here.
		switch($site) {
			case 'luxurylink':
				$match_str="~<a[^>]+><strong>(.*?)</strong>~is";
				break;
			case 'family':
				$match_str = '/familygetaway\.com\/vacation\/((hotels|inns|tour-packages|luxury-cruises|all-inclusive-resorts|lodges|estates-villas)\/[A-Za-z0-9-]+\/[A-Za-z0-9-\'&\+]+)/';
				break;
			default:
				break;
		}
		preg_match_all($match_str, $data, $clientsArr);
		$clientsArr[1] = array_unique($clientsArr[1]);
		$clientIdArr = array();
		$this->Client = ClassRegistry::init("Client");

		foreach ($clientsArr[1] as $clientName) {
			$fn="getClientBySeoUrl";
			if ($site=="luxurylink"){
				$fn="getClientByName";
			}
			if ($client = $this->Client->$fn($clientName)) {
				if (!in_array($client['Client']['clientId'], $clientIdArr)) {
					$clientIdArr[] = $client['Client']['clientId'];
				}
			}
		}
		return $clientIdArr;
*/
	}

	function getClientsFromHtml($data, $site) {
		//preg_match_all("/luxurylink\.com\/luxury-hotels\/.*\?clid=([0-9]+)/", $data, $clients);
		//$clientIds = array_merge(array_unique($clients[1]), array());
		$match_str = '';

		// Right now the $match_str looks similar for both luxurylink and familygetaway (only the URL is different)
		// This may not be the case long term so they are broken out here.
		switch($site) {
			case 'luxurylink':
				$match_str = '/luxurylink\.com\/fivestar\/((hotels|inns|tour-packages|luxury-cruises|all-inclusive-resorts|lodges|estates-villas)\/[A-Za-z0-9-]+\/[A-Za-z0-9-\'&\+]+)/';
				break;
			case 'family':
				$match_str = '/familygetaway\.com\/vacation\/((hotels|inns|tour-packages|luxury-cruises|all-inclusive-resorts|lodges|estates-villas)\/[A-Za-z0-9-]+\/[A-Za-z0-9-\'&\+]+)/';
				break;
			default:
				break;
		}
		preg_match_all($match_str, $data, $clients);

		$clients[1] = array_unique($clients[1]);
		$clientIds = array();
		$this->Client = ClassRegistry::init("Client");

		foreach ($clients[1] as $clientUrl) {
			if ($client = $this->Client->getClientBySeoUrl($clientUrl)) {
				if (!in_array($client['Client']['clientId'], $clientIds)) {
					$clientIds[] = $client['Client']['clientId'];
				}
			}
		}
		return $clientIds;
	}
}
