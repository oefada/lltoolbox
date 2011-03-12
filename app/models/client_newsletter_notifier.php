<?php
App::Import('Model', 'Client');
class ClientNewsletterNotifier extends AppModel {

	var $name = 'ClientNewsletterNotifier';
	var $useTable = false;
	
	function prepareContactDetails() {
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

        $clients = $this->getClientsFromHtml($contents);
        
        $client = new Client;

	    $cl = $client->find('all', array(
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
	
	function getClientsFromHtml($data) {
	    //preg_match_all("/luxurylink\.com\/luxury-hotels\/.*\?clid=([0-9]+)/", $data, $clients);
        //$clientIds = array_merge(array_unique($clients[1]), array());
        
        preg_match_all('/luxurylink\.com\/fivestar\/((hotels|inns|tour-packages|luxury-cruises|all-inclusive-resorts|lodges|estates-villas)\/[A-Za-z0-9-]+\/[A-Za-z0-9-\'&\+]+)/', $data, $clients);
        
        $clientIds = array();
        $clientModel = new Client();
        foreach ($clients[1] as $clientUrl) {
            if ($client = $clientModel->getClientBySeoUrl($clientUrl)) {
                if (!in_array($client['Client']['clientId'], $clientIds)) {
                    $clientIds[] = $client['Client']['clientId'];
                }
            }
        }
	    return $clientIds;
	}
}
?>
