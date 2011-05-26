<?php
class ClientNewsletterNotifierController extends AppController {

	var $name = 'ClientNewsletterNotifier';
	var $helpers = array('Html', 'Form');
	var $components = array('Email');
	
	function beforeFilter() {
	    parent::beforeFilter();
	    $this->set('currentTab', 'reports');
	}

	function index() {
	    if (!empty($this->data)) {
	        $this->ClientNewsletterNotifier->data = $this->data;
            $clients = $this->ClientNewsletterNotifier->prepareContactDetails();
            if (@$this->data['ClientNewsletterNotifier']['approve']) {
                $this->_send($clients, $this->data);
                $this->set('emailSent', true);
            }
            
            $this->set('clients', $clients);
        }
	}
	
	function _send($clients, $data) {//TODO: turn off debug
	    foreach ($clients as $client) {
			$this->Email->reset();

            if (!empty($client['Client']['managerUsername']) && empty($_SERVER['ENV'])) {
               $this->Email->cc[] = $client['Client']['managerUsername'].' <'.$client['Client']['managerUsername'].'@luxurylink.com>';
            }
                
            switch($data['ClientNewsletterNotifier']['site']) {
				case 'luxurylink':
					$this->Email->from = 'Client Marketing <clientmarketing@luxurylink.com>';
					$this->Email->subject = "You are featured in this week's Luxury Link ";

					if ($data['ClientNewsletterNotifier']['themeName']) {
						$this->Email->subject .= '"'.$data['ClientNewsletterNotifier']['themeName'].'" themed ';
					}
					$this->Email->subject .= 'e-Newsletter';
					$this->Email->template = 'client_newsletter_notifier';
					break;
				case 'family':
					$this->Email->from = 'Client Marketing <clientmarketing@familygetaway.com>';
					$this->Email->subject = "You are featured in this week's Family Getaway Newsletter";
					$this->Email->template = 'fg_client_newsletter_notifier';
					break;
				default:
					break;
            }
            
            $this->Email->sendAs = 'both';
                
            $this->set('client', $client['Client']);
            $this->set('clientContact', $client['ClientContact'][0]);
            $this->set('theme', $data['ClientNewsletterNotifier']['themeName']);
            $this->set('url', $data['ClientNewsletterNotifier']['url']);
	        
	        $mainContact = array_shift($client['ClientContact']);                       //first contact is primary, relies on order by clause in model
            if ($_SERVER['ENV'] == 'development' || $_SERVER['ENV'] == 'staging') {
                $this->Email->to = 'livedevmail@luxurylink.com';
            }
            else {
                $this->Email->to = $mainContact['name'].' <'.$mainContact['emailAddress'].'>';
            }
			
	        // iterate through all remaining contacts, array_shift takes care of the first one for us
	        if (empty($_SERVER['ENV'])) {
		        foreach ($client['ClientContact'] as $contact) { //$contact['emailAddress']
					$this->Email->cc[] = $contact['name'].' <'.$contact['emailAddress'].'>';
				}
	        }
	        $this->Email->send();
	    }
	}
}
?>