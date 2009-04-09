<?php
class TravelIdeasController extends AppController {

	var $name = 'TravelIdeas';
	var $helpers = array('Html', 'Form');

	function index($landingPageId = null) {
		if (!$landingPageId) {
			$this->redirect(array('controller' => 'landing_pages', 'action'=>'index'));
		}
		$this->TravelIdea->recursive = 2;
		$this->paginate['conditions']['LandingPage.landingPageId'] = $landingPageId;
		$travelIdeas = $this->paginate();
		$landingPage = $this->TravelIdea->LandingPage->read(null, $landingPageId);
		
		foreach ($travelIdeas as $k => $travelIdea) {
			foreach ($travelIdea['TravelIdeaItem'] as $a => $travelIdeaItem) {
				$travelIdeas[$k]['TravelItems'][$travelIdeaItem['TravelIdeaItemType']['travelIdeaItemTypeName']][] = 
					array(
						'landingPageId' => $landingPageId,
						'travelIdeaId' => $travelIdeaItem['travelIdeaId'],
						'travelIdeaItemId' => $travelIdeaItem['travelIdeaItemId'],
						'travelIdeaItemName' => $travelIdeaItem['travelIdeaItemName']
					);
			}
		}

		$this->set('landingPageId', $landingPageId);		
		$this->set('landingPage', $landingPage);
		$this->set('travelIdeas', $travelIdeas);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Travel Idea.', true));
			$this->redirect(array('controller' => 'landing_pages', 'action'=>'index'));
		}
		$this->set('travelIdea', $this->TravelIdea->read(null, $id));
	}

	function add($landingPageId = null) {
		if (!empty($this->data)) {
			$this->TravelIdea->create();
			if ($this->TravelIdea->save($this->data)) {
				$this->Session->setFlash(__('The Travel Idea has been saved', true));
				$this->redirect(array('controller' => 'travel_ideas', 'action'=>'index', 'id' => $this->data['TravelIdea']['landingPageId']));
			} else {
				$this->Session->setFlash(__('The Travel Idea could not be saved. Please, try again.', true));
			}
		}
		$this->data['TravelIdea']['landingPageId'] = $landingPageId;
		$landingPages = $this->TravelIdea->LandingPage->find('list');
		$this->set('landingPageIds', $landingPages);
	}

	function edit($id = null, $landingPageId = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Travel Idea', true));
		}
		if (!empty($this->data)) {
			if ($this->TravelIdea->save($this->data)) {
				$this->Session->setFlash(__('The Travel Idea has been saved', true));
				$this->redirect(array('controller' => 'travel_ideas', 'action'=>'index', 'id' => $this->data['TravelIdea']['landingPageId']));
			} else {
				$this->Session->setFlash(__('The Travel Idea could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->TravelIdea->read(null, $id);
		}
		$landingPages = $this->TravelIdea->LandingPage->find('list');
		$this->set('landingPageIds', $landingPages);
	}

	function delete($id = null, $landingPageId = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Travel Idea', true));
			$this->redirect(array('controller' => 'travel_ideas', 'action'=>'index', 'id' => $landingPageId));
		}
		if ($this->TravelIdea->del($id)) {
			$this->Session->setFlash(__('Travel Idea deleted', true));
			$this->redirect(array('controller' => 'travel_ideas', 'action'=>'index', 'id' => $landingPageId));
		}
	}

}
?>
