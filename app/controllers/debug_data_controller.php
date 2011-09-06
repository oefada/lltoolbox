<?

class DebugDataController extends AppController{

	var $name='DebugData';
	var $uses=array('DebugData');

	function index(){

		$data=$this->DebugData->find('all');	
		$this->set('data',$data);

	}

}
