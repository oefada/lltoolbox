<?php
/* $Id$ */
/**
 * Multi site behavior.
 *
 * This behavior allows models to be propagated (re-saved) to different databases for "publishing"
 * the models data to more than one database. Also handles updates and deletes.
 *
 * @filesource

 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 */
class MultisiteBehavior extends ModelBehavior {
	private $databases = array('luxurylink' => 'luxurylink_backend', 
								'family' => 'family_backend');
								
	private $sitesColumn = 'site';

	function setup(&$model, $settings = array()) {
		// no special setup required	
		$this->settings[$model->name] = $settings;
		$this->model = &$model;
	}
	
	function afterSave(&$model, $created) {
		$data = $this->model->data;
		$sitesColumn = $this->sitesColumn;		

		//delete from all sites so we can re-save
		if (!$created) {
			$thisBackup = $this->model;

			$this->model->query("DELETE FROM multiSite WHERE model = '{$this->model->alias}' and modelId = ".$data[$this->model->alias][$this->model->primaryKey]);
			
			foreach ($this->databases as $database):
				$this->model->useDbConfig = $database;
				$this->model->delete($data[$this->model->alias][$this->model->primaryKey]);
			endforeach;
		}
		//$this->data overwritten after each save, need to store it here
		
		if ($created) {
			$data[$this->model->alias][$this->model->primaryKey] = $this->model->id;
		}
		
		
		if (!empty($data[$this->model->alias][$sitesColumn]) && !is_array($data[$this->model->alias][$sitesColumn])) {
			$data[$this->model->alias][$sitesColumn] = array($data[$this->model->alias][$sitesColumn]);
		}
		if (is_array($data[$this->model->alias][$sitesColumn])) {
			//iterate through all the databases and save to the selected ones
			foreach ($data[$this->model->alias][$sitesColumn] as $site) {
				$this->model->useDbConfig = $this->databases[$site];

				//$this->model->_schema = null;
				//$this->model->schema();
				$this->model->saveAll($data, array('callbacks' => false));
			}
			$this->model->useDbConfig = 'default';
			$sites = implode(',', $data[$this->model->alias][$sitesColumn]);
			$this->model->query("INSERT INTO multiSite (model, modelId, sites) VALUES('{$this->model->alias}', {$this->model->id}, '$sites')");
		}
	}
	
	/**
	 * Method saves a copy of our object so we can use it in our afterDelete method
	 * @see afterDelete()
	 */
	function beforeDelete() {
		$this->thisBackup = $this->model;	//need to save a copy of this object for later
		return true;
	}

	/**
	 * This method injects the results with the array of the associated databases where this record is also saved in
	 *
	 */
	function afterFind(&$model, $results, $primary) {
		if($primary):
			foreach($results as $k => $result):
				if (isset($result[$this->model->alias][$this->model->primaryKey])) {
					$this->model->useDbConfig = 'default';
					$sites = $this->model->query("SELECT sites FROM multiSite WHERE model = '{$this->model->alias}' AND modelId = {$result[$this->model->alias][$this->model->primaryKey]}");
					if (!empty($sites)){
						$results[$k]['ClientScore']['site'] = explode(',', $sites[0]['multiSite']['sites']);
					}
				}
			endforeach;
		endif;
		return $results;
	}
	
	/**
	 * Method deletes model from main all other databases it may exist in
	 */
	function afterDelete(&$model) {
		//iterate through all the databases and save to the selected ones
		foreach ($this->databases as $field => $database) {
			$this->model->useDbConfig = $database;
			$this->model->schema();
			$db =& ConnectionManager::getDataSource($this->model->useDbConfig);
			$db->delete($this->thisBackup);
		}
		$db =& ConnectionManager::getDataSource('default');
		$db->query("DELETE FROM multiSite WHERE model = '{$this->model->alias}' AND modelId = {$this->model->id}");
	}
}