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

	function trips()
	{
		$this->set('trips', $this->NileGuideTrip->find('all'));
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
				'action' => 'view',
				$url,
			));
		} else {
			$this->Session->setFlash('Could not recognize Nile Guide item.');
			$this->redirect(array('action' => 'index'));
		}
		die('<pre>' . htmlentities(print_r($url, true)));

	}

}
