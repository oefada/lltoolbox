<?php

class SimpleModel extends AppModel {
	var $name = "SimpleModel";
	var $useTable = false;
	var $primaryKey = false;

	function __construct($in) {
		parent::__construct();
		$this->table = $in['id'][0];
		$this->primaryKey = $in['id'][1];
		$parentModel = $in['id'][2];
	}
}
