<?php

class Reporting extends AppModel
{
	public $useTable = false;

	public function __construct()
	{
		parent::__construct();
		$this->setDataSource('reporting');
	}
	
}
	