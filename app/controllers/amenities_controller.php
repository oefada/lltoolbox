<?php
class AmenitiesController extends AppController {

	var $name = 'Amenities';
    var $uses = array('Amenity', 'AmenityType');

	function index() {
        $this->Amenity->recursive = 0;
		$this->paginate['limit'] = 100;
		$this->set('amenities', $this->paginate());
	}
	
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Amenity', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Amenity->save($this->data)) {
				$this->Session->setFlash(__('The Amenity has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The City could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Amenity->read(null, $id);
            $this->set('amenityTypeNames', $this->AmenityType->find('list'));
		}
	}
	
	function add() {
		if (!empty($this->data)) {
			if ($this->Amenity->save($this->data)) {
				$this->Session->setFlash(__('The Amenity has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The City could not be saved. Please, try again.', true));
			}
		}
	}
	
	function auto_complete() {
		$amenities = $this->Amenity->find('all', array(
   		'conditions' => array(
   			'Amenity.amenityName LIKE' => $this->data['Client']['amenity_select'].'%',
   			),
			'limit' => 10,
   			'fields' => array('amenityId', 'amenityName')
   			));
   		$this->set('amenities', $amenities);
   		$this->layout = 'ajax';
  	}


	function view_complete_list_compact() {
		$amenities = $this->Amenity->find('list');
		
		$this->set(compact('amenities'));
   		$this->layout = 'ajax';
  	}

	function search()
	{
	    $inactive = 0;
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
			$inactive = @$_GET['inactive'];
			$this->params['form']['inactive'] = $inactive;
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
			$inactive = @$this->params['named']['inactive'];
			$this->params['form']['inactive'] = $inactive;
		}
		
		$this->set('inactive', $inactive);	

		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);

			$this->Amenity->recursive = -1;
			
			
			$conditions = array("Amenity.amenityName LIKE '%$query%' OR Amenity.amenityId LIKE '%$query%'");

			$results = $this->Amenity->find('all', array('conditions' => $conditions, 'limit' => 5));

			$this->set('query', $query);
			$this->set('results', $results);
			if (isset($this->params['requested'])) {
				return $results;
			} elseif(@$_GET['query'] || @ $this->params['named']['query']) {
				$this->autoRender = false;
				$this->Amenity->recursive = 0;

				$this->paginate = array('conditions' => $conditions);
				$this->set('query', $query);
				$this->set('amenities', $this->paginate());
				$this->render('index');
			}
		endif;
	}
   
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Amenity', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Amenity->del($id)) {
			$this->Session->setFlash(__('Amenity deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
}
?>