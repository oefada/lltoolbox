<?

class IdCreator extends AppModel{

	var $name='IdCreator';
	var $useTable='idCreator';
	var $primaryKey='id';

	// to generate a unique id, 
	// call genId from the controller eg. $this->IdCreator->genId()
	// be sure to add 'IdCreator' to the $uses array at the top of your controller

	function genId(){

		$id_arr['IdCreator']=array('id'=>NULL);
		$this->create();
		$this->save($id_arr);
		return $this->id;

	}

}
