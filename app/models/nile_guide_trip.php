<?php

App::import('Model', array('NileGuideApi'));

class NileGuideTrip extends AppModel
{
	var $name = 'NileGuideTrip';
	var $useDbConfig = 'luxurylink';
	var $useTable = 'nileGuideTrip';
	var $primaryKey = 'id';

	public function import($data)
	{
		$i = 0;
		foreach ($data as $d) {
			$i = ($i + 1) % 100;
			if (!$i) {
				echo 'o';
			}
			$destId = $d['NileGuideDestinationRel']['nileGuideDestinationId'];
			if (is_numeric($destId) && $destId > 0) {
				echo '_';
				foreach (NileGuideApi::fetch('trip', array('searchTerms' => 'destinationId:' . $destId)) as $entry) {
					echo '.';
					if (isset($entry['entry'])) {
						$e = $entry['entry'];
						die(print_r($e,true));
						$this->create();
						$oldId = $this->field('id', array('ngId' => $entry['id']));
						if (is_numeric($oldId) && $oldId > 0) {
							// Existing record
							$this->set('id', $oldId);
						} else {
							// New record
							$this->set('publish', 1);
							$this->set('siteId', 1);
							$this->set('createdDate', date("Y-m-d H:i:s", time()));
						}
						$this->set('ngId', $e['id']);
						$this->set('nileGuideDestinationId', $destId);
						$this->set('title', $e['title']);
						$link = null;
						foreach ($e['link'] as $l) {
							if (is_null($link) && $l['type'] == 'text/html') {
								$this->set('link', $link = $l['href']);
							}
						}
						$this->set('category', $e['category']['term']);
						$this->set('updated', $e['updated']);
						$this->set('content', htmlentities($e['content']['$']));
						$this->set('summary', $e['summary']['$']);
						$this->set('userRating', $e['userRating']);
						$this->set('ngImage', $e['image']['$']);
						echo 'X';
						$this->save($this->data);
					}
				}
			}
		}
		echo "!\n";
	}

}
