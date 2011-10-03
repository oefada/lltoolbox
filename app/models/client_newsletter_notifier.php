<?php
App::Import('Model', 'Client');
class ClientNewsletterNotifier extends AppModel {

	var $name = 'ClientNewsletterNotifier';
	var $useTable = false;

	function prepareContactDetails() {
		$site = $this->data[$this->name]['site'];
	    $url = $this->data[$this->name]['url'];

	    // toolbox can't communicate using www. so we need to direct the connection to one of the servers
	    // in this case we pick web1 randomly
	    $urlToOpen = str_replace('http://www.', 'http://www6.', $url);

	    $handle = fopen($urlToOpen, "r");
        $contents = '';
        while (!feof($handle)) {
          $contents .= fread($handle, 8192);
        }
        fclose($handle);

        $clients = $this->getClientsFromHtml($contents, $site);

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
?>
