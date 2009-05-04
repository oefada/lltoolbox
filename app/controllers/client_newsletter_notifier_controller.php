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
	        foreach ($client['ClientContact'] as $contact) {
	            $this->Email->reset();
	        
    	        $this->Email->from = 'Client Marketing <clientmarketing@luxurylink.com >';
                $this->Email->to = $contact['name'].' <'.$contact['emailAddress'].'>';
                $this->Email->subject = "You are featured in this week's Luxury Link ";
                
                if ($data['ClientNewsletterNotifier']['themeName']) {
                    $this->Email->subject .= '"'.$data['ClientNewsletterNotifier']['themeName'].'" themed ';
                }
                $this->Email->subject .= 'e-Newsletter';
                
                $this->Email->template = 'client_newsletter_notifier';
                $this->Email->sendAs = 'both';
                
                $this->set('client', $client['Client']);
                $this->set('clientContact', $contact);
                $this->set('theme', $data['ClientNewsletterNotifier']['themeName']);
                $this->set('url', $data['ClientNewsletterNotifier']['url']);

                $this->Email->send();
	        }
	        break;
	    }
	}
}
?>