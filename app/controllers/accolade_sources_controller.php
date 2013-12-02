<?php
class AccoladeSourcesController extends AppController {

	var $name = 'AccoladeSources';
	var $scaffold;

	// hide sites from scaffold
	function beforeRender(){
		$this->viewVars['scaffoldFields']=array_diff($this->viewVars['scaffoldFields'],array('sites'));
		return true;
	}
}
?>