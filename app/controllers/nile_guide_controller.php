<?php
class NileGuideController extends AppController
{

	var $name = 'NileGuide';
	var $uses = array(
		'NileGuideTrip',
		'NileGuideTripItinerary',
		'NileGuideDestinationRel',
		'NileGuideAttraction',
	);
	var $helpers = array(
		'Html',
		'Form'
	);

	function index()
	{
	}

	function attraction($id)
	{
		if (isset($this->data['NileGuideAttraction'])) {
			if (isset($this->data['NileGuideAttraction']['id']) && isset($this->data['NileGuideAttraction']['publish'])) {
				$this->NileGuideAttraction->create();
				$this->NileGuideAttraction->save($this->data);
			}
		}
		if ($id) {
			$this->set('attraction', $this->NileGuideAttraction->find('first', array('conditions' => array('ngId' => $id))));
		}
	}

	function url()
	{
		$url = '';
		if (isset($_GET['q'])) {
			$url = $_GET['q'];
			$url = basename($url);
			$url = preg_replace('/-.*$/', '', $url);
		}
		if (is_numeric($url) && $url > 0) {
			$this->redirect(array(
				'action' => 'attraction',
				$url,
			));
		} else {
			$this->Session->setFlash('Could not recognize Nile Guide item.');
			$this->redirect(array('action' => 'index'));
		}
		die('<pre>' . htmlentities(print_r($url, true)));

	}

}
