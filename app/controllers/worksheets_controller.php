<?php
class WorksheetsController extends AppController {

	var $name = 'Worksheets';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Worksheet->recursive = 0;
		$this->set('worksheets', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Worksheet.', true));
			$this->redirect(array('action'=>'index'));
		}
		
		$worksheet = $this->Worksheet->read(null, $id);
		
		if (($worksheet['Worksheet']['isFlake'] || ($worksheet['Worksheet']['worksheetStatusId'] == 7)) && empty($worksheet['WorksheetCancellation']['worksheetId'])) {
			$showCancellation = true;
		} else {
			$showCancellation = false;	
		}
		
		$refundWholeTicket = false;
		foreach ($worksheet['PaymentDetail'] as $pd) {
			if ($pd['refundWholeTicket']) {
				$refundWholeTicket = true;	
				break;
			}	
		}
		
		if (($worksheet['Worksheet']['worksheetStatusId'] == 8 || $refundWholeTicket) && empty($worksheet['WorksheetRefund']['worksheetId'])) {
			$showRefund = true;
		} else {
			$showRefund = false;	
		}

		$validNextBid = false;
		if (!empty($worksheet['WorksheetCancellation']['worksheetId'])) {
			$bids = $this->Worksheet->Offer->Bid->query('SELECT * from bid WHERE offerId = ' . $worksheet['Offer']['offerId'] . ' AND bidId != ' . $worksheet['Worksheet']['bidId'] . ' ORDER BY bidId DESC');
			foreach ($bids as $bid) {
				if (($bid['bid']['bidInactive'] != 1) && ($bid['bid']['bidId'] != $worksheet['Worksheet']['bidId']) && ($bid['bid']['userId'] != $worksheet['Worksheet']['userId'])) {
					$validNextBid = true;
					break;
				}
			}	
		}
		
		$showNewReservationLink = !empty($worksheet['Reservation']['worksheetId']) ? true : false;
		
		$this->set('showNewReservationLink', $showNewReservationLink);
		$this->set('validNextBid', $validNextBid);
		$this->set('showCancellation', $showCancellation);
		$this->set('showRefund', $showRefund);
		$this->set('worksheet', $worksheet);
	}

	function add() {
		if (!empty($this->data)) {
			$this->Worksheet->create();
			if ($this->Worksheet->save($this->data)) {
				$this->Session->setFlash(__('The Worksheet has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Worksheet could not be saved. Please, try again.', true));
			}
		}
		
		$this->set('worksheetStatusIds', $this->Worksheet->WorksheetStatus->find('list'));
		
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Worksheet', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Worksheet->save($this->data)) {
				$this->Session->setFlash(__('The Worksheet has been saved', true));
				$this->redirect(array('action'=>'view', 'id' => $id));
			} else {
				$this->Session->setFlash(__('The Worksheet could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Worksheet->read(null, $id);
		}
		$this->set('worksheetStatusIds', $this->Worksheet->WorksheetStatus->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Worksheet', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Worksheet->del($id)) {
			$this->Session->setFlash(__('Worksheet deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function updateWorksheetStatus($id = null, $worksheetStatusId = null) {
		//bah dont use this function yet
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Worksheet', true));
			//$this->redirect(array('action'=>'index'));				
			return false;
		}	
		$worksheetStatusIds = $this->Worksheet->WorksheetStatus->find('list');
		if (!$worksheetStatusId || !isset($worksheetStatusIds[$worksheetStatusId])) {
			$this->Session->setFlash(__('Invalid attempt to update workstatus', true));
			//$this->redirect(array('action'=>'view', 'id' => $id));				
			return false;
		} else {
			$worksheet['Worksheet']['worksheetId'] = $id;
			$worksheet['Worksheet']['worksheetStatusId'] = $worksheetStatusId;
			if ($this->Worksheet->save($worksheet)) {
				$this->Session->setFlash(__("Workstatus has been updated to \"$worksheetStatusIds[$worksheetStatusId]\"", true));
			} else {
				$this->Session->setFlash(__('Worksheet status has NOT been updated', true));
			}
		}
	}
	
	function autoNewWorksheet($id = null) {
		if ($newWorksheetId = $this->createNewWorksheetFromWorksheet($id)) {
			$this->redirect(array('controller' => 'worksheets', 'action'=>'view', 'id' => $newWorksheetId));				
		} else {
			$this->redirect(array('controller' => 'worksheets', 'action'=>'view', 'id' => $id));			
		}
	}
	
	function createNewWorksheetFromWorksheet($id = null) {
		if (!$id) {
			return false;	
		}
		
		$this->Worksheet->recursive = 0;
		$worksheetData = $this->Worksheet->read(null, $id);
		$newWorksheetData['Worksheet'] = $worksheetData['Worksheet'];
		
		// so we can create a NEW worksheet based on current worksheet
		// change workstatus to NEW, hold same offer info just change bid and user info
		
		unset($newWorksheetData['Worksheet']['worksheetId']);
		$newWorksheetData['Worksheet']['worksheetStatusId'] = 1;
		$newWorksheetData['Worksheet']['parentWorksheetId'] = $id;
		$newWorksheetData['Worksheet']['requestId'] = 0;
		$newWorksheetData['Worksheet']['requestInfo'] = 0;
		$newWorksheetData['Worksheet']['notes'] = "SYSTEM:  This worksheet was automatically created and has a status of NEW.";
		$newWorksheetData['Worksheet']['isFlake'] = 0;
		$newWorksheetData['Worksheet']['paymentAuthDate'] = '0000-00-00 00:00:00';
		$newWorksheetData['Worksheet']['paymentSettleDate'] = '0000-00-00 00:00:00';
		$newWorksheetData['Worksheet']['completedUsername'] = 'AUTO';
		$newWorksheetData['Worksheet']['completedDate'] = '0000-00-00 00:00:00';
		$newWorksheetData['Worksheet']['keepAmount'] = 0;
		$newWorksheetData['Worksheet']['remitAmount'] = 0;
		$newWorksheetData['Worksheet']['comissionAmount'] = 0;
		$newWorksheetData['Worksheet']['requestDate'] = date('Y-m-d H:i:s');
		$newWorksheetData['Worksheet']['userAddress1'] = '';
		$newWorksheetData['Worksheet']['userAddress2'] = '';
		$newWorksheetData['Worksheet']['userAddress3'] = '';
		$newWorksheetData['Worksheet']['userCity'] = '';
		$newWorksheetData['Worksheet']['userState'] = '';
		$newWorksheetData['Worksheet']['userCountry'] = '';
		$newWorksheetData['Worksheet']['userZip'] =	'';

		$offerId = $worksheetData['Offer']['offerId'];
		$bidId = $worksheetData['Worksheet']['bidId'];
		$userId = $worksheetData['Worksheet']['userId'];
		
		if (!$offerId || !$bidId) {
			return false;	
		}
		
		$bids = $this->Worksheet->Offer->Bid->query('SELECT * from bid WHERE offerId = ' . $offerId . ' AND bidId != ' . $bidId . ' ORDER BY bidId DESC');
		
		$foundValidNextBid = false;
		foreach ($bids as $bid) {
			// must have a valid active bid -- get the next top bid
			if (($bid['bid']['bidInactive'] != 1) && ($bid['bid']['bidId'] != $bidId) && ($bid['bid']['userId'] != $userId)) {
				$user = new User();
				$userData = $user->read(null, $bid['bid']['userId']);
				if ($userData) {
					$newWorksheetData['Worksheet']['bidId'] = 			$bid['bid']['bidId'];
					$newWorksheetData['Worksheet']['userId'] = 			$userData['User']['userId'];
					$newWorksheetData['Worksheet']['userFirstName'] = 	$userData['User']['firstName'];
					$newWorksheetData['Worksheet']['userLastName'] = 	$userData['User']['lastName'];
					$newWorksheetData['Worksheet']['userEmail1'] = 		$userData['User']['email'];
					$newWorksheetData['Worksheet']['userWorkPhone'] = 	$userData['User']['workPhone'];
					$newWorksheetData['Worksheet']['userHomePhone'] = 	$userData['User']['homePhone'];
					$newWorksheetData['Worksheet']['userMobilePhone'] = $userData['User']['mobilePhone'];
					$newWorksheetData['Worksheet']['userFax'] = 		$userData['User']['fax'];
					
					if (!empty($userData['Address'])) {
						$newWorksheetData['Worksheet']['userAddress1'] =	$userData['Address']['address1'];
						$newWorksheetData['Worksheet']['userAddress2'] = 	$userData['Address']['address2'];
						$newWorksheetData['Worksheet']['userAddress3'] = 	$userData['Address']['address3'];
						$newWorksheetData['Worksheet']['userCity'] = 		$userData['Address']['city'];
						$newWorksheetData['Worksheet']['userState'] = 		$userData['Address']['stateName'];
						$newWorksheetData['Worksheet']['userCountry'] = 	$userData['Address']['countryName'];
						$newWorksheetData['Worksheet']['userZip'] =			$userData['Address']['postalCode'];
					}
					$foundValidNextBid = true;
					break;	
				}
			}	
		}
		
		if ($foundValidNextBid) {
			$this->Worksheet->create();
			if ($this->Worksheet->save($newWorksheetData)) {
				$this->Session->setFlash(__('The original worksheet was cancelled and a NEW worksheet has been created based on the original.', true));
				return $this->Worksheet->getLastInsertID();
			} else {
				$this->Session->setFlash(__('There was an error while creating the new worksheet.', true));
				return false;
			}
		} else {
			$this->Session->setFlash(__('Could not find the next eligible bid -- a new worksheet was NOT created.', true));
			return false;	
		}
		
		/*
		
		//attach  containable behavior, can also be done in the model through an actsAs
		$this->Worksheet->Behaviors->attach('Containable');
		
		//choose which child/sibling models we want to return besides the current one
		$this->Worksheet->contain('Offer');
		
		//do a normal read
		$worksheetData = $this->Worksheet->read(null, $id);
		$offerId = $worksheetData['Offer']['offerId'];
		
		//we only get Worksheet and whatevr we contained by, in this case, Offer
		debug($worksheetData);
		*/
	}

}
?>