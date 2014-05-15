<?php

class PpvNoticesController extends AppController {

	var $name = 'PpvNotices';
	var $helpers = array('Html', 'Form', 'Javascript', 'Ajax');
	var $uses = array('PpvNotice', 'Ticket');

	function index() {
		$this->PpvNotice->recursive = 0;
		$this->set('ppvNotices', $this->paginate());
	}

	/**
	 * Dummy data and display for coding the template
	 * 
	 * @return TODO
	 */
	public function preview(){

		$this->set("fontFamily", "font-family:Helvetica,maradival,Sans-Serif");
		$this->set("siteId", 1);
		$this->set("siteEmail", "luxurylink.com");
		$this->set("siteName", "LuxuryLink");
		$this->set("siteUrl", "http://www.luxurylink.com");
		$this->set("additionalClients", false);
		$this->set("isAuction", false);
		$this->set("additionalClients", false);
		$this->set("clientNameP", "Test Client Name");
		$this->set("locationDisplay", "Test of location display value");
		$this->set("sitePhone", "123-123-1234");
		$this->set("sitePhoneLocal", "333-444-1234");
		$this->set("pdpUrl", "http://www.luxurylink.com/fivestar/hotels/test-location/hotel-testerosa");
		$img="http://photos.luxurylink.us/images/sho_4fcee4e4/8455_9015-auto-578/image-8455_9015.jpg";
		$this->set("clientImagePath", $img);

		$file="42_43_abandoned_cart.html";
		$this->set('fileName', $file);
		$this->autoLayout=false;
		Configure::write('debug',0);
		$this->render("preview");

	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PpvNotice.', true));
			$this->redirect(array('action'=>'index'));
		}

		$this->set('ppvNotice', $this->PpvNotice->read(null, $id));
		$this->layout = "ajax";
	}

	function add($ticketId, $id, $clientId = null) {

		// web service for tickets for getting/sending ppv
		$ticket = $this->Ticket->read(null, $ticketId);

		$webservice_live_url = Configure::read("Url.Ws"). "/web_service_tickets/?wsdl";

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
					$val=(isset($this->data['PpvNotice']['emailSubject']) ? $this->data['PpvNotice']['emailSubject'] : "");
					$data['override_email_subject']  = $val; 

					//for hotel beds originally but will work for any client, get attachments
					if ($_FILES) {

						$fileAttachArray = array();
						$fileAttachArrayName = array();
						$fileTypeAttachArray = array();

						foreach ($_FILES as $key => $value) {
							$file=$_SERVER{'DOCUMENT_ROOT'} . '/attachments/' . $_FILES[$key]['name'];
							if (move_uploaded_file($_FILES[$key]['tmp_name'],  $file)) {
								$fileAttachArray[] = $_FILES[$key]['name'];
								$fileTypeAttachArray[] = $_FILES[$key]['type'];
							}
						}

						$data['emailAttachment'] = $fileAttachArray;
						$data['emailAttachmentType'] = $fileTypeAttachArray;

					}

					if ($clientId) {
						$data['clientId']		= $clientId;
					}

					$data_json_encoded = json_encode($data);
					$soap_client = new SoapClient($webservice_live_url, array("exception" => 1));
					$response = $soap_client->ppv($data_json_encoded);
					// var_dump($response);
					// exit;
					if( in_array($id, array(1,23)) ) {
						$updateTicket = array();
						$updateTicket['ticketId'] = $ticketId;
						$updateTicket['ticketStatusId'] = 4;
						$this->Ticket->save($updateTicket);
					}

					$this->Session->setFlash(__('The Ppv/Notice has been sent.', true));
					$this->redirect(
						array(
							'controller' => 'tickets', 
							'action'=>'view', 
							'id' => $this->data['PpvNotice']['ticketId']
						)
					);
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
			$data['sender_sig']		 = 1;
			switch ($_SESSION['Auth']['AdminUser']['username']) {
				case 'jsanchez':
					$sender_ext = "424.835.3620";
					$sender_title = 'Travel Concierge';
					break;
				case 'dvojdany':
					$sender_ext = "424.835.3642";
					$sender_title = 'Travel Concierge';
					break;
				default:
					$data['sender_sig'] = 0;
					break;
			}

			$data['sender_sig_line']	= $_SESSION['Auth']['AdminUser']['displayname'] . ', ' . $sender_title;
			$data['sender_email']		= $_SESSION['Auth']['AdminUser']['mail'];
			$data['sender_ext']			= $sender_ext;
		}

		$this->set('promo', $this->Ticket->getTicketPromoData($ticketId));

		if (in_array($id, array(2,4,10,27,28,55,24,29,33,31))) {
			$clientContacts = $this->Ticket->getClientContacts($ticketId, $clientId);
			$this->data['PpvNotice']['emailTo'] = $clientContacts['contact_to_string'];
			$this->data['PpvNotice']['emailCc'] = $clientContacts['contact_cc_string'];
			
			// if sending Cancelation Request PPV (id=29), add cancellations@luxurylink|familygetaway.com to CC
			if ($id == 29) {
				if ($this->data['PpvNotice']['emailCc'] == '') {
					if ($ticket['Ticket']['siteId'] == 1) {
						$this->data['PpvNotice']['emailCc'] = 'cancellations@luxurylink.com';
					} else if ($ticket['Ticket']['siteId'] == 2) {
						$this->data['PpvNotice']['emailCc'] = 'cancellations@familygetaway.com';
					}
				} else {
					if ($ticket['Ticket']['siteId'] == 1) {
						$this->data['PpvNotice']['emailCc'] .= ', cancellations@luxurylink.com';
					} else if ($ticket['Ticket']['siteId'] == 2) {
						$this->data['PpvNotice']['emailCc'] .= ', cancellations@familygetaway.com';
					}
				}
			}
			
		} else {
			$q="SELECT user.email FROM ticket INNER JOIN user USING (userId) WHERE ticketId =  $ticketId LIMIT 1";
			$ticket_user_email = $this->Ticket->query($q);
			if (!isset($ticket_user_email[0]['user']['email'])){
				$this->data['PpvNotice']['emailTo']='devmail@luxurylink.com';
			}else{
				$this->data['PpvNotice']['emailTo'] = $ticket_user_email[0]['user']['email'];
			}
		}

		$this->set('clientIdParam', $clientIdParam);
		
		if ($id == 1 || $id == 33) {
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
		
		App::import('Controller', 'WebServiceTickets');
		
		$wstc = new WebServiceTicketsController;
		$wstc->constructClasses();
		$response = $wstc->ppv($data);

		if (in_array($id, array(26,27,28,55,33))) {
			$data['returnSubject'] = true;
			$this->set('editSubject', $wstc->ppv($data));
			
			if($id == 33) {
				$this->Ticket->recursive = 0;
				$ticket = $this->Ticket->read(null, $ticketId);
				$str='Please Confirm this Luxury Link Booking - CHANGE OF DATE - ACTION REQUIRED - ';
				$str.=$ticket['Ticket']['userFirstName'].' '.$ticket['Ticket']['userLastName'];
				$this->set('editSubject', $str);
			}
		}
		
		$ppv_only = 0;
		if (isset($_GET['ppv_only'])) {
			$this->layout = "ajax";
			$ppv_only = 1;
			Configure::write("debug",0);
		}
		
		$this->set('ppv_only',$ppv_only);
	 	$this->set('ppv_body_text', $response);
		$this->set('ticketId', $ticketId);
	}

}
