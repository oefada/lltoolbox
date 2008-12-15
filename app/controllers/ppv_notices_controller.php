<?php

App::import('Vendor', 'nusoap_client/lib/nusoap');

class PpvNoticesController extends AppController {

	var $name = 'PpvNotices';
	var $helpers = array('Html', 'Form', 'Javascript', 'Ajax');
	var $uses = array('PpvNotice', 'Ticket');

	function index() {
		$this->PpvNotice->recursive = 0;
		$this->set('ppvNotices', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PpvNotice.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('ppvNotice', $this->PpvNotice->read(null, $id));
	}

	function add($ticketId, $id) {
		
		// web service for tickets for getting/sending ppv
		$webservice_live_url = 'http://toolboxdev.luxurylink.com/web_service_tickets?wsdl';
		//$webservice_live_url = 'http://192.168.100.111/web_service_tickets?wsdl';
		$webservice_live_method_name = 'ppv';
		$webservice_live_method_param = 'in0';
		
		if (!empty($this->data)) {
			
				$data = array();
				$data['ticketId'] 			= $this->data['PpvNotice']['ticketId'];
				$data['send'] 				= 1;
				$data['autoBuild'] 			= 0;
				$data['manualEmailBody']	= $this->data['PpvNotice']['emailBody'];
				$data['returnString'] 		= 0;
				$data['ppvNoticeTypeId'] 	= $this->data['PpvNotice']['ppvNoticeTypeId'];
				
				$data_json_encoded = json_encode($data);
				$soap_client = new nusoap_client($webservice_live_url, true);
        		$response = $soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));
				
				$this->Session->setFlash(__('The Ppv/Notice has been sent.', true));
				$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $this->data['PpvNotice']['ticketId']));
				
		}
		$this->set('ppvNoticeTypeIds', $this->PpvNotice->PpvNoticeType->find('list'));
		$this->data['PpvNotice']['ticketId'] = $ticketId;
		$this->data['PpvNotice']['ppvNoticeTypeId'] = $id;

		$data = array();
		$data['ticketId'] 			= $ticketId;
		$data['send'] 				= 0;
		$data['autoBuild'] 			= 1;
		$data['manualEmailBody']	= 0;
		$data['returnString'] 		= 1;
		$data['ppvNoticeTypeId'] 	= $id;

		$data_json_encoded = json_encode($data);
		$soap_client = new nusoap_client($webservice_live_url, true);
        $response = $soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));
                   
        //$this->data['PpvNotice']['emailBody'] = htmlspecialchars($response, ENT_QUOTES, "UTF-8");
        //$this->data['PpvNotice']['emailBody'] = preg_replace('/[^a-z0-9]/', '', $response); 
     	$this->set('ppv_body_text', $response);
	}

	/*
	// NO EDIT OR DELETE FOR PPV -- JUST SENDING OUT

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PpvNotice', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->PpvNotice->save($this->data)) {
				$this->Session->setFlash(__('The PpvNotice has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The PpvNotice could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PpvNotice->read(null, $id);
		}
		$tickets = $this->PpvNotice->Ticket->find('list');
		$this->set(compact('tickets'));
		
		$this->set('ppvNoticeTypeIds', $this->PpvNotice->PpvNoticeType->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for PpvNotice', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PpvNotice->del($id)) {
			$this->Session->setFlash(__('PpvNotice deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	*/

}
?>