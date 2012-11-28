<?php
class CmsController extends AppController {

	var $name = 'Cms';
	var $helpers = array('Time','Html','Form');
	var $uses = array('Cms');
	
	function index() {
		$result = $this->Cms->getCmsList();
		
		$this->set('cmsResults', $result);
	}
	
	function edit($id = null) {
		
		echo $env = Configure::read('Url.LL');
		
		if(empty($this->data)){
			$this->data = $this->Cms->getCms($id);
		} else {
			// save logic 
			$this->Cms->saveCmsEdit($id, $this->data['Cms']);
			$this->data = $this->Cms->getCms($id);
			
			$this->set('cmsSaved', 1);
		}
		
		$this->set('cmsId', $this->params['pass']['0']);
		$this->set('cmsEnv', $env);
	}
}


