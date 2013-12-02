<?php

class NileGuideDestinationRel extends AppModel
{
	var $name = 'NileGuideDestinationRel';
	var $useDbConfig = 'luxurylink';
	var $useTable = 'nileGuideDestinationRel';
	var $primaryKey = 'id';

	public function import($data)
	{
		$i = 0;
		foreach ($data['feed']['entry'] as $k => $v) {
			$i = ($i + 1) % 100;
			if (!$i) {
				echo '.';
			}
			$this->create();
			$this->set('id', $this->field('id', array('nileGuideDestinationId' => $v['id'])));
			$this->set('nileGuideDestinationId', $nileGuideDestinationId = $v['id']);
			$this->set('nileGuideLocation', $v['title']);
			$this->save($this->data);
		}
		echo "!\n";
	}

}
