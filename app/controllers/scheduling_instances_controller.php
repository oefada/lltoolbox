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
		if (!empty($this->data)) {
			$this->SchedulingInstance->create();
			if ($this->SchedulingInstance->save($this->data)) {
				$this->Session->setFlash(__('The SchedulingInstance has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SchedulingInstance could not be saved. Please, try again.', true));
			}
		}
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
	    $metrics = $this->SchedulingInstance->query('SELECT OfferLive.offerId, TicketStatus.ticketStatusName, MAX(Bid.bidAmount) as maxBidAmount,'.
	                                                ' MAX(Bid.bidDateTime) as maxBidDateTime, COUNT(*) as numBids, COUNT(DISTINCT Bid.userId) as numUniqueBidders'.
	                                                ' FROM bid as Bid LEFT JOIN offer as Offer ON (Bid.offerId = Offer.offerID)'.
	                                                ' LEFT JOIN offerLive AS OfferLive ON (OfferLive.offerId = Offer.offerId)'.
	                                                ' LEFT JOIN ticket as Ticket ON (Offer.offerId = Ticket.offerId)'.
	                                                ' LEFT JOIN ticketStatus as TicketStatus ON(Ticket.ticketStatusId = TicketStatus.ticketStatusId)'.
	                                                ' WHERE Offer.schedulingInstanceId = '.$id);
	    $this->SchedulingInstance->recursive = -1;
        $schedulingInstance = $this->SchedulingInstance->find('first', array('conditions' => array('SchedulingInstance.schedulingInstanceId ' => $id)));

		$this->set('metrics', $metrics[0]);
		$this->set('schedulingInstance', $schedulingInstance);
	}

}
?>