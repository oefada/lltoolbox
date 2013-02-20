<?php
class NileGuideTripItinerary extends AppModel
{
	var $name = 'NileGuideTripItinerary';
	var $useDbConfig = 'luxurylink';
	var $useTable = 'nileGuideTripItineraries';
	var $primaryKey = 'id';

	public function import($data)
	{

		die("NileGuideTripItinerary import\n\n" . print_r($data, true));

	}

}
