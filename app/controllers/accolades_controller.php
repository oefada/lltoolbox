<?php
class AccoladesController extends AppController {

	var $name = 'Accolades';
	var $helpers = array('Html', 'Form');
    function beforeFilter() {
        parent::beforeFilter();
		$this->set('currentTab', 'siteMerchandising');
		$this->pageTitle = 'Accolades Tool';
    }
    
	function index() {
		$this->Accolade->recursive = 0;
		$this->set('accolades', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Accolade.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('accolade', $this->Accolade->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Accolade->create();
			if ($this->Accolade->save($this->data)) {
				$this->Session->setFlash(__('The Accolade has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Accolade could not be saved. Please, try again.', true));
			}
		}
		$accoladeSourceIds = $this->Accolade->AccoladeSource->find('list');

		$this->set(compact('accoladeSourceIds'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Accolade', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Accolade->save($this->data)) {
				$this->Session->setFlash(__('The Accolade has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Accolade could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Accolade->read(null, $id);
		}
		$accoladeSourceIds = $this->Accolade->AccoladeSource->find('list');

		$this->set(compact('accoladeSourceIds'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Accolade', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Accolade->del($id)) {
			$this->Session->setFlash(__('Accolade deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function search()
	{
	    $inactive = 0;
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
			$inactive = @$_GET['inactive'];
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
		}
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);

			$this->Accolades->recursive = -1;

			$conditions = array("Accolade.clientId = '$query' OR AccoladeSource.accoladeSourceName LIKE '$query%' OR Client.name LIKE '$query%'");
			
			//if (!$inactive) {
			//    $conditions['Accolade.inactive'] = 0;
			//}
			$results = $this->Accolade->find('all', array('conditions' => $conditions, 'limit' => 5));

			$this->set('query', $query);
			$this->set('results', $results);
			
			if (isset($this->params['requested'])) {
				return $results;
			} elseif(@$_GET['query'] || @ $this->params['named']['query']) {
				$this->autoRender = false;
				$this->Client->recursive = 0;

				$this->paginate = array('conditions' => $conditions);
				$this->set('query', $query);
				$this->set('accolades', $this->paginate());
				$this->render('index');
			}
		endif;
	}
}
?>