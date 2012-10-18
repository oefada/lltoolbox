<?php
class BidsController extends AppController {

	var $name = 'Bids';
	var $helpers = array('Html', 'Form');

	function __construct() {
		parent::__construct();
		$this->set('hideSidebar',true);
	}
	
	function index() {
		$this->Bid->recursive = 0;
		$this->set('bids', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Bid.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('bid', $this->Bid->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Bid->create();
			if ($this->Bid->save($this->data)) {
				$this->Session->setFlash(__('The Bid has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Bid could not be saved. Please, try again.', true));
			}
		}
		$users = $this->Bid->User->find('list');
		$offers = $this->Bid->Offer->find('list');
		$this->set(compact('users', 'offers'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Bid', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Bid->save($this->data)) {
				$this->Session->setFlash(__('The Bid has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Bid could not be saved. Please, try again.', true));
			}
		}
		
		if (empty($this->data)) {
			$this->data = $this->Bid->read(null, $id);
		}
		
		$offers = $this->Bid->Offer->find('list', array(
			'conditions'=>array('Offer.offerId'=>$this->data['Offer']['offerId'])
			)
		);
		$this->set(compact('offers'));
		
	}

	function search()
	{
		$this->autoRender = false;
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
		}
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);
			$conditions = array('OR' => array('CONCAT(User.firstName, " ", User.lastName) LIKE' => "%$query%", 'Bid.bidId LIKE' => "%$query%",'Bid.offerId LIKE' => "$query%", 'Bid.userId LIKE' => "$query%", 'User.firstName LIKE' => "$query%", 'User.lastName LIKE' => "$query%"));

			if($_GET['query'] ||  $this->params['named']['query']) {
				$this->autoRender = false;
				$this->Client->recursive = 0;

				$this->paginate = array('conditions' => $conditions);
				$this->set('query', $query);
				$this->set('bids', $this->paginate());
				$this->render('index');
			} else {
				$this->Client->recursive = -1;
				$results = $this->User->find('all', array('conditions' => $conditions, 'limit' => 5));
				$this->set('query', $query);
				$this->set('results', $results);
				return $results;
			}
		endif;
	}


}
?>
