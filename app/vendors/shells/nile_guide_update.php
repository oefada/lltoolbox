<?php

App::import('Core', array('Model'));
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

		//die(print_r(NileGuideApi::fetch('destination', null,false), true));
		//die('zzz');

		// DestinationRel
		$this->log('Fetching destinations...');
		$this->NileGuideDestinationRel->import(NileGuideApi::fetch('destination'));

		die('xxx');

		// Trip (depends on destinationRel)
		$this->log('Fetching trips...');
		$this->NileGuideTrip->import(NileGuideApi::fetch('trip'));

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

class NileGuideApi
{

	private static $basePath = 'http://www.nileguide.com/service/';
	private static $apiKey = 'c24af97e-7a2e-46aa-95fc-9b3c5cde6c8f';
	private static $headers = array();

	public static function fetch($rest, $params = array(), $format = 'json')
	{
		switch($format) {
			case 'json' :
				self::$headers['Accept'] = 'application/json';
				break;
			case 'xml' :
				self::$headers['Accept'] = 'application/xml';
				break;
		}
		$params['key'] = self::$apiKey;
		$path = self::$basePath . $rest . '?';
		$prefix = '';
		foreach ($params as $k => $v) {
			$path .= $prefix . urlencode($k) . '=' . urlencode($v);
			$prefix = '&';
		}
		$cacheKey = sha1(serialize(array(
			'path' => $path,
			'params' => $params,
			'headers' => self::$headers,
		)));
		if (!is_dir('/tmp/nileguide/')) {
			mkdir('/tmp/nileguide/');
		}
		$cacheFile = '/tmp/nileguide/cache-' . $cacheKey . '.delme';
		if (file_exists($cacheFile) && ((time() - filemtime($cacheFile)) < (60 * 60 * 24 * 7))) {
			echo "NileGuideApi: Cache hit for: $rest\n";
			$data = file_get_contents($cacheFile);
		} else {
			echo "NileGuideApi: Not cached, fetching from API: $rest\n";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $path);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if (self::$headers) {
				$headerArray = array();
				foreach (self::$headers as $k => $v) {
					$headerArray[] = $k . ': ' . $v;
				}
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
			}
			$data = curl_exec($ch);
			curl_close($ch);
			file_put_contents($cacheFile, $data);
		}
		switch($format) {
			case 'json' :
				return json_decode($data, true);
				break;
			case 'xml' :
				return simplexml_load_string($data);
				break;
			default :
				return $data;
		}
	}

}
