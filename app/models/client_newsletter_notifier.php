<?php
App::Import('Model', 'Client');
class ClientNewsletterNotifier extends AppModel {

	var $name = 'ClientNewsletterNotifier';
	var $useTable = false;
	
	function prepareContactDetails() {
	    $url = $this->data[$this->name]['url'];
	    $handle = fopen($url, "r");
        $contents = '';
        while (!feof($handle)) {
          $contents .= fread($handle, 8192);
        }
        fclose($handle);
        
        $clients = $this->getClientsFromHtml($contents);
        
        $client = new Client;
	    $cl = $client->find('all', array(
	                                    'fields' => 'name',
	                                    'conditions' => array('Client.clientId' => $clients),
	                                    'contain' => array('ClientContact' => array('conditions' => array('ClientContact.clientContactTypeId' => 2))),
	                                    ));
	                                    
	                                    
	    return $cl;
	}
	
	function getClientsFromHtml($data) {
	    preg_match_all("/luxurylink\.com\/luxury-hotels\/.*\?clid=([0-9]+)/", $data, $clients);

	    $clientIds = array_merge(array_unique($clients[1]), array());
	    
	    return $clientIds;
	}
}
?>
