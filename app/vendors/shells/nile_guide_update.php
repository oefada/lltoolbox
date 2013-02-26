<?php

App::import('Core', array('Model'));
App::import('Model', array('NileGuideApi'));
App::import('Model', array('NileGuideAttraction'));
App::import('Model', array('NileGuideDestinationRel'));
App::import('Model', array('NileGuideTripItinerary'));
App::import('Model', array('NileGuideTrip'));

class NileGuideUpdateShell extends Shell
{
	private $logfile = 'nile_guide';

	public function initialize()
	{
		$this->NileGuideAttraction = new NileGuideAttraction;
		$this->NileGuideDestinationRel = new NileGuideDestinationRel;
		$this->NileGuideTripItinerary = new NileGuideTripItinerary;
		$this->NileGuideTrip = new NileGuideTrip;
	}

	public function main()
	{
		$this->log("/////////// Nile Guide Updater //////////");

		/*
		// DestinationRel
		$this->log('Fetching destinations...');
		$this->NileGuideDestinationRel->import(NileGuideApi::fetch('destination'));
		*/
		
		// Trip (depends on destinationRel)
		$this->log('Fetching trips...');
		$this->NileGuideTrip->import($this->NileGuideDestinationRel->find('all'));

		die("\n\n\nxxx\n\n\n");

		// Trip Itinerary (depends on trip)
		$this->log('Fetching itineraries...');
		$this->NileGuideTripItinerary->import(NileGuideApi::fetch('trip'));

		// Attractions (depends on trip itineraries)
		$this->log('Fetching attractions...');
		$this->NileGuideAttraction->import(NileGuideApi::fetch('trip', array('searchTerms' => 'destinationId:29')));
	}

	public function log($message)
	{
		parent::log($message, $this->logfile);
		echo date('Y-m-d H:i:s') . ' - ' . $message . "\n";
	}

}
