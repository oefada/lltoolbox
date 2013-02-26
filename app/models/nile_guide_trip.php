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
				echo '.';
			}
			$destId = $d['NileGuideDestinationRel']['nileGuideDestinationId'];
			if (is_numeric($destId) && $destId > 0) {
				$destination = NileGuideApi::fetch('destination/' . $destId);
				$e = $destination['entry'];
				$this->create();
				$this->set('id', $this->field('id', array('nileGuideDestinationId' => $e['id'])));
				$this->set('nileGuideDestinationId', $nileGuideDestinationId = $e['id']);
				$this->set('title', $e['title']);
				$this->set('link', $e['link']['href']);
				$this->set('category', $e['category']['term']);
				$this->set('publish', true);
				$this->set('siteId', 1);
				$this->save($this->data);
			}
		}
		echo "!\n";
	}

}
