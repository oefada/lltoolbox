<?php

class NileGuideDestinationRel extends AppModel
{
	var $name = 'NileGuideDestinationRel';
	var $useDbConfig = 'luxurylink';
	var $useTable = 'nileGuideDestinationRel';
	var $primaryKey = 'id';

	public function import($data)
	{
		foreach ($data['feed']['entry'] as $k => $v) {
			echo "$k\n";
			$this->create();
			$this->set('id', $this->field('id', array('nileGuideDestinationId' => $v['id'])));
			/*
			$this->set('destinationId', null);
			$this->set('destinationName', null);
			 */
			$this->set('nileGuideDestinationId', $nileGuideDestinationId = $v['id']);
			$this->set('nileGuideLocation', $v['title']);
			$this->save($this->data);
		}
	}

}
