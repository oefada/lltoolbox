<?php
class TravelIdeaItemsController extends AppController {

	var $name = 'TravelIdeaItems';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->TravelIdeaItem->recursive = 0;
		$this->set('travelIdeaItems', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid TravelIdeaItem.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('travelIdeaItem', $this->TravelIdeaItem->read(null, $id));
	}

	function add($travelIdeaId = null, $landingPageId = null) {
		if (!empty($this->data)) {
			$this->TravelIdeaItem->create();
			if ($this->TravelIdeaItem->save($this->data)) {
				$this->Session->setFlash(__('The Travel Idea Item has been saved', true));
				$this->redirect(array('controller' => 'travel_ideas', 'action'=>'index', 'id' => $landingPageId));
			} else {
				$this->Session->setFlash(__('The TravelIdeaItem could not be saved. Please, try again.', true));
			}
		}
		$travelIdeas = $this->TravelIdeaItem->TravelIdea->find('list');
		$travelIdeaItemTypes = $this->TravelIdeaItem->TravelIdeaItemType->find('list');
		$this->data['TravelIdeaItem']['travelIdeaId'] = $travelIdeaId;
		$this->set('travelIdeaIds', $travelIdeas);
		$this->set('travelIdeaItemTypeIds', $travelIdeaItemTypes);
		$this->set('travelIdeaId', $travelIdeaId);
		$this->set('landingPageId', $landingPageId);
	}

	function edit($id = null, $landingPageId = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid TravelIdeaItem', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->TravelIdeaItem->save($this->data)) {
				$this->Session->setFlash(__('The TravelIdeaItem has been saved', true));
				$this->redirect(array('controller' => 'travel_ideas', 'action'=>'index', 'id' => $landingPageId));
			} else {
				$this->Session->setFlash(__('The TravelIdeaItem could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->TravelIdeaItem->read(null, $id);
		}
		$travelIdeas = $this->TravelIdeaItem->TravelIdea->find('list');
		$travelIdeaItemTypes = $this->TravelIdeaItem->TravelIdeaItemType->find('list');
		$this->set('travelIdeaIds', $travelIdeas);
		$this->set('travelIdeaItemTypeIds', $travelIdeaItemTypes);
		$this->set('tiId', $id);
		$this->set('landingPageId', $landingPageId);
	}

	function delete($id = null, $landingPageId = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for TravelIdeaItem', true));
			$this->redirect(array('controller' => 'travel_ideas', 'action'=>'index', 'id' => $landingPageId));
		}
		if ($this->TravelIdeaItem->del($id)) {
			$this->Session->setFlash(__('TravelIdeaItem deleted', true));
			$this->redirect(array('controller' => 'travel_ideas', 'action'=>'index', 'id' => $landingPageId));
		}
	}

}
?>
