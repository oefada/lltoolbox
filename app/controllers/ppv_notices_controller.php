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

	function add($ticketId, $id, $clientId = null) {
		
		// web service for tickets for getting/sending ppv
		$webservice_live_url = 'http://toolbox.luxurylink.com/web_service_tickets?wsdl';
		if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
			$webservice_live_url = 'http://alee-toolboxdev.luxurylink.com/web_service_tickets?wsdl';
		} elseif (stristr($_SERVER['HTTP_HOST'], 'stage')) {
			$webservice_live_url = 'http://stage-toolbox.luxurylink.com/web_service_tickets?wsdl';
		}
		$webservice_live_method_name = 'ppv';
		$webservice_live_method_param = 'in0';
		
		if (!empty($this->data)) {
				
				if (empty($this->data['PpvNotice']['emailTo'])) {
					$this->Session->setFlash(__('The Email To field cannot be empty', true));
				} else {
					if (isset($_SESSION['Auth']['AdminUser']['username'])) {
						$ppvInitials = $_SESSION['Auth']['AdminUser']['username'];
					} else {
						$ppvInitials = 'TOOLBOX';	
					}
					$data = array();
					$data['ticketId'] 			= $this->data['PpvNotice']['ticketId'];
					$data['send'] 				= 1;
					$data['manualEmailBody']	= str_replace('<p>&nbsp;</p>', '', $this->data['PpvNotice']['emailBody']);
					$data['returnString'] 		= 0;
					$data['ppvNoticeTypeId'] 	= $this->data['PpvNotice']['ppvNoticeTypeId'];
					$data['initials']			= $ppvInitials;
					$data['override_email_to']  = $this->data['PpvNotice']['emailTo'];
					$data['override_email_cc']  = $this->data['PpvNotice']['emailCc'];
					$data['override_email_subject']  = $this->data['PpvNotice']['emailSubject'];
	
					if ($clientId) {
						$data['clientId']		= $clientId;	
					}
					
					$data_json_encoded = json_encode($data);
					$soap_client = new nusoap_client($webservice_live_url, true);
	        		$response = $soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));
					
					$this->Session->setFlash(__('The Ppv/Notice has been sent.', true));
					$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $this->data['PpvNotice']['ticketId']));
				}
		}
		$this->set('ppvNoticeTypeIds', $this->PpvNotice->PpvNoticeType->find('list'));
		$this->data['PpvNotice']['ticketId'] = $ticketId;
		$this->data['PpvNotice']['ppvNoticeTypeId'] = $id;

		$data = array();
		$data['ticketId'] 			= $ticketId;
		$data['send'] 				= 0;
		$data['manualEmailBody']	= 0;
		$data['returnString'] 		= 1;
		$data['ppvNoticeTypeId'] 	= $id;
		if ($clientId) {
			$data['clientId']		= $clientId;	
			$clientIdParam = "/$clientId";
		} else {
			$clientIdParam = '';	
		}
	
		if (isset($_SESSION['Auth']['AdminUser']['username'])) {
			$sender_ext = $sender_title = '';
			$data['sender_sig']         = 1;
			switch ($_SESSION['Auth']['AdminUser']['username']) {
				case 'jsanchez':
					$sender_ext = 149;
					$sender_title = 'Travel Concierge';
					break;
				case 'dvojdany':
					$sender_ext = 147;
					$sender_title = 'Customer Care Concierge';
					break;
				default:
					$data['sender_sig'] = 0;
					break;
			}

			$data['sender_sig_line']    = $_SESSION['Auth']['AdminUser']['displayname'] . ', ' . $sender_title;
			$data['sender_email']		= $_SESSION['Auth']['AdminUser']['mail'];
			$data['sender_ext']			= 'Ext ' . $sender_ext;
		}

		$this->set('promo', $this->Ticket->getTicketPromoData($ticketId));

		if (in_array($id, array(2,4,10,27,28))) {
			$clientContacts = $this->Ticket->getClientContacts($ticketId);
			$this->data['PpvNotice']['emailTo'] = $clientContacts['contact_to_string'];
			$this->data['PpvNotice']['emailCc'] = $clientContacts['contact_cc_string'];
		} else {
			$ticket_user_email = $this->Ticket->query("SELECT user.email FROM ticket INNER JOIN user USING (userId) WHERE ticketId =  $ticketId LIMIT 1");
			$this->data['PpvNotice']['emailTo'] = $ticket_user_email[0]['user']['email'];
		}

		if (in_array($id, array(26,27,28))) {
			$this->set('editSubject', 1);
		}

		$this->set('clientIdParam', $clientIdParam);
		$data_json_encoded = json_encode($data);
		$soap_client = new nusoap_client($webservice_live_url, true);
        $response = $soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));
                   
     	$this->set('ppv_body_text', $response);
		$this->set('ticketId', $ticketId);
		if ($id == 1) {
			// reservation confirmation 
			$reservation = $this->Ticket->query("SELECT * FROM reservation WHERE ticketId = $ticketId");
			if (!empty($reservation)) {
				$this->set('hasResData', 1);
				$this->set('reservation', $reservation[0]['reservation']);
			} else {
				$this->set('hasResData', 0);
			}
			$this->set('isResConf', 1);
		}
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
