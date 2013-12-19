<?php

class Reporting extends AppModel
{
	public $useTable = false;
	//public $useDbConfig = 'reporting';

    public function __construct(){
        parent::__construct();
        if (ISDEV == true ||ISSTAGE == true){
            $this->useDbConfig = 'default';
        }else{
            $this->useDbConfig = 'reporting';
        }
    }
}
	