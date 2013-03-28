<?php

class RefundRequestsController extends AppController {

	var $name = 'RefundRequests';
	var $uses = array('RefundRequest', 'PaymentDetail', 'Ticket', 'PaymentType', 'PromoCode');

	var $handlingFee = 40;
	var $keepOrRemitList = array('K'=>'Keep', 'R'=>'Remit');
	var $refundOrCOFList = array('R'=>'Refund', 'C'=>'COF', 'B'=>'Both');

	function beforeFilter() {
		parent::beforeFilter();
	    $this->set('handlingFee', $this->handlingFee);
        $this->set('keepOrRemitList', $this->keepOrRemitList);
        $this->set('refundOrCOFList', $this->refundOrCOFList);
	}	

	function index() {
		
		$displayCsv = (!empty($this->data) && $this->data['RefundRequest']['csv'] == 1) ? true : false;
		$status = (!empty($this->data) && $this->data['RefundRequest']['f_status'] != '') ? $this->data['RefundRequest']['f_status'] : false;
		$dateField = (!empty($this->data) && $this->data['f_Date']['field'] != '') ? $this->data['f_Date']['field'] : false;
		$dateStart = (!empty($this->data) && $this->data['f_Date']['start'] != '') ? $this->data['f_Date']['start'] : false;
		$dateEnd = (!empty($this->data) && $this->data['f_Date']['end'] != '') ? $this->data['f_Date']['end'] : false;

		if (empty($this->data)) {
			$status = 'HC';
			$this->data = array('RefundRequest' => array('f_status' => 'HC'));
		}

		$q = "SELECT * FROM
			  (
			  SELECT RefundRequest.*, RefundReason.refundReasonName, RefundRequestStatus.description, Ticket.userLastName, Ticket.billingPrice, Ticket.packageId, OfferType.offerTypeName, PaymentDetail.ppCardNumLastFour, PaymentDetail.ppResponseDate
			  FROM refundRequest RefundRequest
			  INNER JOIN refundRequestStatus RefundRequestStatus ON RefundRequest.refundRequestStatusId = RefundRequestStatus.refundRequestStatusId
			  INNER JOIN refundReason RefundReason ON RefundRequest.refundReasonId = RefundReason.refundReasonId
			  INNER JOIN ticket Ticket ON RefundRequest.ticketId = Ticket.ticketId
			  INNER JOIN offerType OfferType ON Ticket.offerTypeId = OfferType.offerTypeId
			  LEFT JOIN paymentDetail PaymentDetail ON RefundRequest.paymentDetailId = PaymentDetail.paymentDetailId
			  WHERE 1 = 1";

		if ($status == 'HC') {
			$q .= " AND RefundRequest.refundRequestStatusId IN (1, 2)";
		} elseif ($status) {
			$q .= " AND RefundRequest.refundRequestStatusId = " . $status;
		}
		if ($dateField && $dateStart) {
			$q .= " AND RefundRequest." . $dateField . " >= '" . $dateStart . "'";
		}			
		if ($dateField && $dateEnd) {
			$q .= " AND RefundRequest." . $dateField . " < '" . $dateEnd . "'";
		}
		
		$q .= ") RefundInfo";

		if ($displayCsv) {
			$q .= " INNER JOIN
			  (SELECT cpr.packageId, GROUP_CONCAT(c.name) AS `name` 
			  FROM `client` c
			  INNER JOIN clientLoaPackageRel cpr USING(clientId)
			  GROUP BY cpr.packageId
			  ) ClientInfo USING(packageId)
			  LEFT JOIN			   
			  (SELECT ticketId, SUM(ppBillingAmount) AS ccBillingAmount 
			   FROM paymentDetail 
			   WHERE isSuccessfulCharge = 1
			   AND paymentTypeId = 1
			   GROUP BY ticketId 
			  ) BillingInfoCC USING(ticketId)
			  LEFT JOIN			   
			  (SELECT ticketId, SUM(ppBillingAmount) AS gcBillingAmount 
			   FROM paymentDetail 
			   WHERE isSuccessfulCharge = 1
			   AND paymentTypeId = 2
			   GROUP BY ticketId 
			  ) BillingInfoGC USING(ticketId)
			  LEFT JOIN			   
			  (SELECT ticketId, SUM(ppBillingAmount) AS cofBillingAmount 
			   FROM paymentDetail 
			   WHERE isSuccessfulCharge = 1 
			   AND paymentTypeId = 3
			   GROUP BY ticketId 
			  ) BillingInfoCOF USING(ticketId)";
		}

		$q .= " ORDER BY RefundInfo.refundRequestId DESC";

		$results = $this->RefundRequest->query($q);
		$this->set('refundRequests', $results);
		
		$allowApprovedDelete = false;
		$currentUser = $this->LdapAuth->user();
		if ($currentUser['LdapUser']['samaccountname'] == 'kferson') {
			$allowApprovedDelete = true;
		}
		$this->set('allowApprovedDelete', $allowApprovedDelete);
            
		if ($displayCsv) {
            $this->set('keepOrRemitList', $this->keepOrRemitList);
            $this->set('refundOrCOFList', $this->refundOrCOFList);
			Configure::write('debug', '0');
			$this->viewPath .= '/csv';
			$this->layoutPath = 'csv';
		
		} else {
			$refundStatuses = $this->RefundRequest->RefundRequestStatus->find('list');
			$refundStatuses['HC'] = 'Hide Completed';
			$this->set('refundStatuses', $refundStatuses);
			$this->set('hideSidebar', true);
		}
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid RefundRequest.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->data = $this->RefundRequest->read(null, $id);
		$refundInfo = $this->prepRefundInfoByTicketId($this->data['RefundRequest']['ticketId']);
		$this->set('refundInfo', 	$refundInfo);
		$editorAccess = $this->hasEditorAccess();
		if ($editorAccess && $this->data['RefundRequest']['refundRequestStatusId'] != 3) {
			$this->set('displayRefundRequestEditLink', true);
		}
		$this->set('pageVersion', 	'V');
	}

	function add($ticketId) {		
		if (!empty($this->data)) {
			$currentUser = $this->LdapAuth->user();
			$this->data['RefundRequest']['refundRequestStatusId'] = 1;
			$this->data['RefundRequest']['createdBy'] = $currentUser['LdapUser']['samaccountname'];
			$this->data['RefundRequest']['dateCreated'] = date('Y-m-d G:i:s');
			$this->RefundRequest->create();
			
			if ($this->data['RefundRequest']['refundOrCOF'] == '') {
				$this->Session->setFlash(__('The Refund Request could not be saved.<br/>Refund / COF Select is required', true));
			} elseif ($this->data['RefundRequest']['cancelFeeLL'] == '') {
				$this->Session->setFlash(__('The Refund Request could not be saved.<br/>LL Cancel Fee is required', true));
			} else {
				if ($this->RefundRequest->save($this->data)) {
					$this->Session->setFlash(__('The Refund Request has been saved', true));

					$note = date("n/j/Y") . " -- added refund request\n\n";
					$q = "UPDATE ticket SET ticketNotes = CONCAT(?, IFNULL(ticketNotes, '')) WHERE ticketId = ?";
					$this->RefundRequest->query($q, array($note, $this->data['RefundRequest']['ticketId']));

					$this->redirect(array('action'=>'index'));
				} else {
					$errors = '';
					foreach($this->RefundRequest->invalidFields() as $f) {
						$errors .= '<br>' . $f;
					}
					$this->Session->setFlash(__('The Refund Request could not be saved.' . $errors, true));
				}
			}
			
			$refundInfo = $this->prepRefundInfoByTicketId($ticketId);
			
		} else {
			if (!$ticketId) {
				$this->Session->setFlash(__('No Ticket Found.', true));
				$this->redirect(array('action'=>'index'));
			}
			
			$refundInfo = $this->prepRefundInfoByTicketId($ticketId);
			
			// prefill info
			$this->data['RefundRequest']['refundHandlingFeeFlag'] = 0;
			$this->data['RefundRequest']['ticketId'] = $ticketId;
			$this->data['RefundRequest']['refundTotal'] = $refundInfo['ticket']['Ticket']['billingPrice'] - $refundInfo['promoDeduction'];
			$this->data['RefundRequest']['promoDeduction'] = $refundInfo['promoDeduction'];
			if ($refundInfo['creditCards']) {
				$cardKeys = array_keys($refundInfo['creditCards']);
				$this->data['RefundRequest']['paymentDetailId'] = $cardKeys[0];
			}
			if ($refundInfo['ticket']['Reservation']) {
				$this->data['RefundRequest']['arrivalDate'] = $refundInfo['ticket']['Reservation']['arrivalDate'];
			}
			if ($refundInfo['ticket']['Cancellation']) {
				$this->data['RefundRequest']['cancellationNumber'] = $refundInfo['ticket']['Cancellation']['cancellationNumber'];
				$this->data['RefundRequest']['cancelledWith'] = $refundInfo['ticket']['Cancellation']['confirmedBy'];
			}			
		}
		
		$editorAccess = $this->hasEditorAccess();
		$this->set('editorAccess', $editorAccess);
		
		$refundReasons = $this->RefundRequest->RefundReason->find('list', array('conditions' => array("inactive" => 0)));
		$refundStatuses = $this->RefundRequest->RefundRequestStatus->find('list');
		$this->set(compact('refundInfo', 'refundReasons', 'refundStatuses'));
		$this->set('pageVersion', 	'A');
	}

	function edit($id = null) {

		if (!empty($this->data)) {
			if ($this->data['RefundRequest']['markCompleted'] == '1') {
				$this->data['RefundRequest']['refundRequestStatusId'] = 3;
				$this->data['RefundRequest']['dateCompleted'] = date('Y-m-d G:i:s');
				$currentUser = $this->LdapAuth->user();
				$this->data['RefundRequest']['completedBy'] = $currentUser['LdapUser']['samaccountname'];
			}
			if ($this->RefundRequest->save($this->data)) {
				$this->Session->setFlash(__('The Refund Request has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$errors = '';
				foreach($this->RefundRequest->invalidFields() as $f) {
					$errors .= '<br>' . $f;
				}
				$this->Session->setFlash(__('The Refund Request could not be saved.' . $errors, true));
				$this->redirect(array('action'=>'edit', $this->data['RefundRequest']['refundRequestId']));
			}			
		} else {
			if (!$id) {
				$this->Session->setFlash(__('No Request Found.', true));
				$this->redirect(array('action'=>'index'));
			}
			$this->data = $this->RefundRequest->read(null, $id);
		}
		
		$editorAccess = $this->hasEditorAccess();
		$accountingAccess = $this->hasAccountingAccess();

		if ($this->data['RefundRequest']['refundRequestStatusId'] > 1 && !$editorAccess) {
			$this->Session->setFlash(__('You do not have permission to edit the request.', true));
			$this->redirect(array('action'=>'index'));	
		}

		if ($this->data['RefundRequest']['refundRequestStatusId'] == 3) {
			$this->Session->setFlash(__('This request has been completed and cannot be edited.', true));
			$this->redirect(array('action'=>'view', $id));	
		}

		if ($editorAccess && $this->data['RefundRequest']['refundRequestStatusId'] == 1) {
			$this->set('displayRefundRequestApproveLink', true);
		}

		if ($accountingAccess && $this->data['RefundRequest']['refundRequestStatusId'] == 2) {
			$this->set('displayRefundRequestCompleteLink', true);
		}
				
		$refundInfo = $this->prepRefundInfoByTicketId($this->data['RefundRequest']['ticketId']);
		$refundReasons = $this->RefundRequest->RefundReason->find('list', array('conditions' => array("inactive" => 0)));
		$refundStatuses = $this->RefundRequest->RefundRequestStatus->find('list');
		$this->set(compact('refundInfo', 'refundReasons', 'refundStatuses'));
		$this->set('editorAccess', $editorAccess);
		$this->set('accountingAccess', $accountingAccess);
		$this->set('pageVersion', 	'E');	
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Request', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->RefundRequest->del($id)) {
			$this->Session->setFlash(__('Request deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

	function setApproved($id = null) {
		$editorAccess = $this->hasEditorAccess();
		if (!$editorAccess) {
			$this->Session->setFlash(__('You do not have permission to approve the request.', true));
			$this->redirect(array('action'=>'index'));	
		}
		
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Request', true));
			$this->redirect(array('action'=>'index'));
		}
		
		$this->data = $this->RefundRequest->read(null, $id);
		
		if ($this->data['RefundRequest']['refundRequestStatusId'] != 1) {
			$this->Session->setFlash(__('Status must be New for approval action.', true));
			$this->redirect(array('action'=>'index'));	
		}
		
		$currentUser = $this->LdapAuth->user();
		$this->data['RefundRequest']['refundRequestStatusId'] = 2;
		$this->data['RefundRequest']['dateApproved'] = date('Y-m-d G:i:s');
		$this->data['RefundRequest']['approvedBy'] = $currentUser['LdapUser']['samaccountname'];
		$this->RefundRequest->save($this->data);
		$this->Session->setFlash(__('Request approved', true));
		$this->redirect(array('action'=>'edit', $id));
	}

	function setComplete($id = null) {
		$accountingAccess = $this->hasAccountingAccess();
		if (!$accountingAccess) {
			$this->Session->setFlash(__('You do not have permission to complete the request.', true));
			$this->redirect(array('action'=>'index'));	
		}
		
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Request', true));
			$this->redirect(array('action'=>'index'));
		}
		
		$this->data = $this->RefundRequest->read(null, $id);
		
		if ($this->data['RefundRequest']['refundRequestStatusId'] != 2) {
			$this->Session->setFlash(__('Status must be Approved for complete action.', true));
			$this->redirect(array('action'=>'index'));	
		}		
		
		$currentUser = $this->LdapAuth->user();
		$this->data['RefundRequest']['refundRequestStatusId'] = 3;
		$this->data['RefundRequest']['dateCompleted'] = date('Y-m-d G:i:s');
		$this->data['RefundRequest']['completedBy'] = $currentUser['LdapUser']['samaccountname'];		
		$this->RefundRequest->save($this->data);
		$this->Session->setFlash(__('Request completed', true));
		$this->redirect(array('action'=>'edit', $id));
	}

	
	private function hasEditorAccess() {
		$currentUser = $this->LdapAuth->user();
		$access = ($this->hasAccountingAccess() || $currentUser['LdapUser']['samaccountname'] == 'bjensen' || $currentUser['LdapUser']['samaccountname'] == 'jcross') ? true : false;
		return $access;
	}

	private function hasAccountingAccess() {
		$currentUser = $this->LdapAuth->user();
		$access = (in_array('Accounting',$currentUser['LdapUser']['groups']) || in_array('Geeks',$currentUser['LdapUser']['groups'])) ? true : false;
		return $access;
	}

	private function prepRefundInfoByTicketId($ticketId) {
		$info = array();

		$this->Ticket->recursive = 1;
		$ticket = $this->Ticket->read(null, $ticketId);
		
		// $this->Offer->recursive = 1;
		// $offer = $this->Offer->read(null, $ticket['Ticket']['offerId']);
		
		$totalPaid = 0;
		$cards = array();
		foreach($ticket['PaymentDetail'] as $k=>$v) {
			$paymentType = $this->PaymentType->read(null, $v['paymentTypeId']);
			$ticket['PaymentDetail'][$k]['paymentTypeName'] = $paymentType['PaymentType']['paymentTypeName'];
			if ($v['isSuccessfulCharge'] == 1) {
				$amount = isset($v['ppBillingAmount']) && $v['ppBillingAmount'] != 0 ? $v['ppBillingAmount'] : $v['paymentAmount'];
				$ticket['PaymentDetail'][$k]['amount'] = $amount;
				$totalPaid += $amount;
				if ($v['paymentTypeId'] == 1) {
					$cards[$v['paymentDetailId']] = 'xxxx-xxxx-xxxx-' . $v['ppCardNumLastFour'] . ' - ' . $v['ccType'];
				}
			} else {
				unset($ticket['PaymentDetail'][$k]);
			}
		}
		$info['totalPaid'] = $totalPaid;
		$info['creditCards'] = $cards;


		$this->PromoCode->recursive = 1;
		$promoDeduction = 0;
		foreach($ticket['PromoTicketRel'] as $k=>$v) {	
			$promoInfo = $this->PromoCode->read(null, $v['promoCodeId']);
			if ($promoInfo['Promo'][0]['amountOff'] > 0) {
				$promoDeduction += $promoInfo['Promo'][0]['amountOff'];
				$promoInfo['label'] = '$' . $promoInfo['Promo'][0]['amountOff'];
			} else {
				$promoDeduction += ($promoInfo['Promo'][0]['percentOff'] / 100) * $ticket['Ticket']['billingPrice'];
				$promoInfo['label'] = $promoInfo['Promo'][0]['percentOff'] . '%';
			}
			$ticket['PromoTicketRel'][$k]['promoInfo'] = $promoInfo;
		}
		
		$info['promoDeduction'] = $promoDeduction;
		$info['client'] = $this->Ticket->getClientsFromPackageId($ticket['Ticket']['packageId']);		
		$info['ticket'] = $ticket;
		return $info;
	}



}
