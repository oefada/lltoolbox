<?php
class SchedulingInstancesController extends AppController {

	var $name = 'SchedulingInstances';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->SchedulingInstance->recursive = 0;
		$this->set('schedulingInstances', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid SchedulingInstance.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('schedulingInstance', $this->SchedulingInstance->read(null, $id));
	}

	function add() {
	    if (empty($this->data['SchedulingInstance']['schedulingMasterId']) && empty($this->params['named']['schedulingMasterId'])) {
	        die("Need scheduling master ID");
	    }
	     
		if (!empty($this->data)) {
		    $this->SchedulingInstance->SchedulingMaster->id = $this->data['SchedulingInstance']['schedulingMasterId'];
		    $schedulingMaster = $this->SchedulingInstance->SchedulingMaster->read();
		    
		    $startDate = $this->data['SchedulingInstance']['startDate'];
		    $startDate = $startDate['year'].'-'.$startDate['month'].'-'.$startDate['day'].' '.$startDate['hour'].':'.$startDate['min'].':00 '.$startDate['meridian'];

		    $this->data['SchedulingInstance']['endDate'] = date('Y-m-d H:m:00A', strtotime("+{$schedulingMaster['SchedulingMaster']['numDaysToRun']} days", strtotime($startDate)));
		    
			$this->SchedulingInstance->create();
			$valid = true;
			if (strtotime($startDate) < strtotime('+30 minutes')) {
			    $this->SchedulingInstance->invalidate('startDate', 'Start date must be atleast 30 minutes in the future'); 
			    $valid = false;
			}
			
			if ($valid === true) {
			if ($this->SchedulingInstance->save($this->data)) {
                $schedulingMaster['SchedulingMaster']['iterations']++;
    		    $this->SchedulingInstance->SchedulingMaster->save($schedulingMaster);
    		    
				$this->Session->setFlash(__('The SchedulingInstance has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SchedulingInstance could not be saved. Please, try again.', true));
			}
		    } else {
				$this->Session->setFlash(__('The SchedulingInstance could not be saved. Please, correct the errors below.', true));
			}
		} else {
		    $this->data['SchedulingInstance']['schedulingMasterId'] = $this->params['named']['schedulingMasterId'];
		    $this->SchedulingInstance->SchedulingMaster->id = $this->params['named']['schedulingMasterId'];
		    $schedulingMaster = $this->SchedulingInstance->SchedulingMaster->read();
		}
		
		$this->set('schedulingMaster', $schedulingMaster);
	}
	
	function auto_extend() {
	    if (empty($this->data['SchedulingInstance']['schedulingMasterId']) && empty($this->params['named']['schedulingMasterId'])) {
	        die("Need scheduling master ID");
	    }
	    
	    $this->SchedulingInstance->SchedulingMaster->id = $this->params['named']['schedulingMasterId'];
	    $schedulingMaster = $this->SchedulingInstance->SchedulingMaster->read();
	    
	    if (!empty($this->data)){
	    $schedulingInstance = $this->SchedulingInstance->query("SELECT MAX(endDate) as maxEndDate FROM schedulingInstance WHERE schedulingMasterId = {$this->SchedulingInstance->SchedulingMaster->id}");
	    
	    $startDate = strtotime("{$schedulingInstance[0][0]['maxEndDate']} +1 hour");
	    
	    if ($startDate < time()) {
	        $startDate = strtotime("+1 hour");
	    }
	    
	    if (date('G', $startDate) >= 17) {
		    $startDate = strtotime('+1 day', $startDate);   //push to next day
		    $startDate = date('Y-m-d', $startDate);         //convert to just Y-m-d
		    $startDate = $startDate.' 07:00:00';            //set time to 8am
		    $startDate = strtotime($startDate);             //save new date as timestamp
		}
		
		$endDate = strtotime('+' . $schedulingMaster['SchedulingMaster']['numDaysToRun'] . ' days', $startDate);
	    
	    while ($this->SchedulingInstance->_isHoliday($endDate)) {
	        $endDate = strtotime('+1 day', $endDate);
	    }
	    
	    $schedulingInstance['SchedulingInstance'] = array('schedulingMasterId' => $schedulingMaster['SchedulingMaster']['schedulingMasterId'],
	                                                        'startDate' => date("Y-m-d H:00:00", $startDate),
	                                                        'endDate' => date("Y-m-d H:00:00", $endDate)
	                                                    );
	    
	    if ($this->SchedulingInstance->save($schedulingInstance)) {
            $schedulingMaster['SchedulingMaster']['iterations']++;
            $this->SchedulingInstance->SchedulingMaster->save($schedulingMaster);
            if ($this->RequestHandler->isAjax()) {
				$this->Session->setFlash(__('The new instance was saved', true), 'default', array(), 'success');
				$this->set('closeModalbox', true);
			}
        } else {
            if ($this->RequestHandler->isAjax()) {
				$this->Session->setFlash(__('The new instance could not be saved', true), 'default', array(), 'error');
				$this->set('closeModalbox', true);
			}
        }
        }
        $this->set('schedulingMaster', $schedulingMaster);
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid SchedulingInstance', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->SchedulingInstance->save($this->data)) {
				$this->Session->setFlash(__('The SchedulingInstance has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SchedulingInstance could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->SchedulingInstance->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SchedulingInstance', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->SchedulingInstance->del($id)) {
			$this->Session->setFlash(__('SchedulingInstance deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	/**
	 * Method called from Prototip using ajax. Finds all of the performance metrics and passes them into a view file.
	 * View file is shown in a tooltip.
	 *
	 * @param int $id of the scheduling instance to grab performance metrics for
	 */
	function performanceTooltip($id) {
	    $metrics = $this->SchedulingInstance->query('SELECT TicketStatus.ticketStatusName, MAX(Bid.bidAmount) as maxBidAmount,'.
	                                                ' MAX(Bid.bidDateTime) as maxBidDateTime, COUNT(*) as numBids, COUNT(DISTINCT Bid.userId) as numUniqueBidders'.
	                                                ' FROM bid as Bid LEFT JOIN offer as Offer ON (Bid.offerId = Offer.offerID)'.
	                                                ' LEFT JOIN offerLuxuryLink AS OfferLive ON (OfferLive.offerId = Offer.offerId)'.
	                                                ' LEFT JOIN ticket as Ticket ON (Offer.offerId = Ticket.offerId)'.
	                                                ' LEFT JOIN ticketStatus as TicketStatus ON(Ticket.ticketStatusId = TicketStatus.ticketStatusId)'.
	                                                ' WHERE Offer.schedulingInstanceId = '.$id);
	    $this->SchedulingInstance->recursive = -1;
        $schedulingInstance = $this->SchedulingInstance->find('first', array('conditions' => array('SchedulingInstance.schedulingInstanceId ' => $id)));
        $offer = $this->SchedulingInstance->Offer->find('first', array('conditions' => array('Offer.schedulingInstanceId ' => $id)));

		$this->set('metrics', $metrics[0]);
		$this->set('schedulingInstance', $schedulingInstance);
		$this->set('offer', $offer);
	}

}
?>
