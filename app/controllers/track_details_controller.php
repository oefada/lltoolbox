<?php
class TrackDetailsController extends AppController {

	var $name = 'TrackDetails';
	var $helpers = array('Html', 'Form', 'Number');
	var $uses = array('TrackDetail', 'Track', 'Loa', 'Ticket', 'RevenueModel');

	function index() {
		$this->TrackDetail->recursive = 0;
		$this->set('trackDetails', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid TrackDetail.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('trackDetail', $this->TrackDetail->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->TrackDetail->create();
			if ($this->TrackDetail->save($this->data)) {
				$this->Session->setFlash(__('The TrackDetail has been saved', true));
				$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $this->data['TrackDetail']['ticketId']));
			} else {
				$this->Session->setFlash(__('Track Detail was not saved.  Please check your input and try again.', true));
			}
		}
		
		// fetch and process all the information need for this 'next' track record
		// -------------------------------------------------------------------------------------------------
		$trackDetailExists = false;
		$track = $this->TrackDetail->getTrackRecord($this->params['ticketId']);
		if (!$track) {
			$this->Session->setFlash(__('There is no TRACK found for this Package.', true));
		} else {
			$this->set('trackDetails', $this->TrackDetail->getAllTrackDetails($track['trackId']));
			$trackDetailExists = $this->TrackDetail->findExistingTrackTicket($track['trackId'], $this->params['ticketId']);
			if ($trackDetailExists) {
				$this->Session->setFlash(__('The revenue for this ticket has already been allocated.', true));
			} else {
				$this->data['TrackDetail'] = $this->TrackDetail->getNewTrackDetailRecord($track, $this->params['ticketId']);
			}
		}
		
		// set some vars
		// -------------------------------------------------------------------------------------------------
		$this->set('track', $track);
		$this->set('revenueModels', $this->RevenueModel->find('list'));
		$this->set('ticketId', $this->params['ticketId']);
		$this->set('trackDetailExists', $trackDetailExists);
	}

	function updateTracks() {
		$sql = "select distinct(t.ticketId) from ticket t inner join paymentDetail pd on pd.ticketId = t.ticketId and pd.isSuccessfulCharge = 1 where t.created > '2009-02-21' order by t.ticketId";
		$result = $this->Ticket->query($sql);
		
		$c = count($result);
		$c1 = $c2 = $c3 = $c4 = 0;
		
		foreach ($result as $k => $v) {
			$ticketId = $v['t']['ticketId'];
			$track = $this->TrackDetail->getTrackRecord($ticketId);
			$new_track_detail = array();
			if (!empty($track)) {
				$c2++;
				$trackDetailExists = $this->TrackDetail->findExistingTrackTicket($track['trackId'], $ticketId);
				$new_track_detail = $this->TrackDetail->getNewTrackDetailRecord($track, $ticketId);
				if ($new_track_detail && !$trackDetailExists) {
					$c4++;
					//$this->TrackDetail->create();
					//if (!$this->TrackDetail->save($new_track_detail)) {
						//print_r($new_track_detail);	
					//}
				} else {
					$c3++;
				}
			} else {
				$c1++;	
			}
		}
		
		echo "COUNT: $c<br />";
		echo "COUNT NO TRACK: $c1<br />";
		echo "COUNT TRACK: $c2<br />";
		echo "COUNT TD NO EXISTS: $c4<br />";
		echo "COUNT TD EXISTS: $c3<br />";
		
		die('COMPLETE!');	
	}
	
	/*
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid TrackDetail', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->TrackDetail->save($this->data)) {
				$this->Session->setFlash(__('The TrackDetail has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The TrackDetail could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->TrackDetail->read(null, $id);
		}
	}
	*/
}
?>