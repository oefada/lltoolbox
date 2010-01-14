<?php
/* SVN FILE: $Id: app_model.php 6311 2008-01-02 06:33:52Z phpnut $ */

/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 6311 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-01 22:33:52 -0800 (Tue, 01 Jan 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.app
 */
class AppModel extends Model{
    
    var $sites = array(1 => 'luxurylink',
                       2 => 'family');
    
	/**
	* Constructor just sets some defaults for Luxury Link and calls the parent constructor.
	*/
	function __construct($id = false, $table = null, $ds = null) {
		if ($this->name === null && get_class($this) != 'AppModel' && get_class($this) != 'Aco' && get_class($this) != 'Aro' && get_class($this) != 'ApiClass' && get_class($this) != 'ApiPackage') {
			$this->name = get_class($this);
		}
		
		//only do this on models other than AppModel
		if($this->name != 'AppModel' && get_class($this) != 'Aco' && get_class($this) != 'Aro' && get_class($this) != 'ApiClass' && get_class($this) != 'ApiPackage'):
			//Use the model name as the table name (forget about the cake plurals)
			if ($this->useTable !== false) {
				if ($this->useTable === null) {
					//set the table as modelName
					$this->useTable = Inflector::variable($this->name);
				}
			}

			//Use modelNameID as the ID key (overrite using 'id' for cake)
			if ($this->primaryKey === null) {
				$this->primaryKey = Inflector::variable($this->name).'Id';
			}
		endif;

		parent::__construct($id, $table, $ds);
	}
    
    //convert sites field into a Cake-readable array if the sites column is a set field    
    function afterFind($results) {
        if (Model::hasField('sites') && $this->isMultisite()) {
            foreach($results as &$result) {
                if (isset($result[$this->name]['sites'])) {
                    if (in_array('sites', array_keys($result[$this->name])) && !is_array($result[$this->name]['sites'])) {
                        $result[$this->name]['sites'] = explode(',', $result[$this->name]['sites']);
                    }
                }
            }
        }
        return $results;
    }
    
    //convert sites array into a string that can be saved to a set field
    function beforeSave() {
        if (Model::hasField('sites') && $this->isMultisite()) {
            if (!empty($this->data[$this->name]['sites']) && is_array($this->data[$this->name]['sites'])) {
                $this->data[$this->name]['sites'] = implode(',', $this->data[$this->name]['sites']);
            }
        }
        return true;
    }
    
    //push this model and any defined associations to the front-end databases
    function afterSave($created) {
        if ($this->isMultisite()) {            
            //limit the returned associated models to those defined in the $containModels variable in the main model
            if (isset($this->containModels)) {
                $this->contain($this->containModels);
            }
            //retrieve model and associations from toolbox database
            $modelData = $this->find('first', array('conditions' => array($this->name.'.'.$this->primaryKey => $this->id)));
            //if this model inherits its sites from another, get them here
            if (isset($this->inheritsFrom)) {
                $parentModel = new $this->inheritsFrom['modelName'];
                $sites = $parentModel->find('first', array('conditions' => array($parentModel->name.'.'.$parentModel->primaryKey => $modelData[$this->name][$parentModel->primaryKey]),
                                                           'fields' => array($this->inheritsFrom['siteField'])));
                $modelData[$this->name][$this->inheritsFrom['siteField']] = $sites[$parentModel->name][$this->inheritsFrom['siteField']];
                $siteField = $this->inheritsFrom['siteField'];
            }
            if (!isset($siteField)) {
                $siteField = (Model::hasField('sites')) ? 'sites' : 'siteId';
            }
            //save model's sites to a variable before unsetting it from the array
            if (is_numeric($modelData[$this->name][$siteField])) {
                $modelSites = array($this->sites[$modelData[$this->name][$siteField]]);
            }
            elseif (is_array($modelData[$this->name][$siteField])) {
                $modelSites = $modelData[$this->name][$siteField];
            }
            else {
                return;
            }
            unset($modelData[$this->name][$siteField]);
            //loop through all sites and save to front-end if applicable
            foreach ($this->sites as $site) {
                $this->saveToFrontEndDb($modelData, $site, $modelSites, $created);
            }
            $this->useDbConfig = 'default';
        }
    }
    
   //delete this model and any defined associations from front-end databases
   function afterDelete() {
        if ($this->isMultisite()) {
            if (isset($this->containModels)) {
                $this->contain($this->containModels);
            }
            foreach($this->sites as $site) {
                $this->useDbConfig = $site;
                $result = $this->find('first', array('fields' => $this->primaryKey,
                                      'conditions' => array($this->primaryKey => $this->id)));
                if (!empty($result)) {
                    $this->deleteFromFrontEnd($result, $site);
                }
            } 
        }
        $this->useDbConfig = 'default';
   }
    
    function saveToFrontEndDb($modelData, $site, $modelSites, $created) {
        $this->useDbConfig = $site;
        //this if-statement handles cases when a site has been removed from this record; 
        //if the record exists in the front-end database it will delete it and associated models
        if (!$created) {
            $exists = $this->find('first', array('fields' => $this->primaryKey,
                                  'conditions' => array($this->primaryKey => $modelData[$this->name][$this->primaryKey])));
            if (!empty($exists)) {
                if (!in_array($site, $modelSites)) {
                    $this->deleteFromFrontEnd($modelData, $site);
                    return;
                }
            }
        }
        //save record to the front-end database only if the $site is valid for the record
        if (in_array($site, $modelSites)) {
            if ($this->name == 'ImageRoomGradeRel') {
                debug($site);
                debug($modelData[$this->name]);
                die();
            }
            $fields = $this->getFields($site);
            $this->create();
            $this->save($modelData[$this->name], array('callbacks' => false, 'fieldList' => $fields));
            if (isset($this->containModels)) {
                $this->saveAssocModels($modelData, $site);
            }
        }
        $this->useDbConfig = 'default';
    }
    
    function saveAssocModels($modelData, $site) {
        foreach($this->containModels as $assocModel) {
            $this->$assocModel->useDbConfig = $site;
            $this->$assocModel->create();
            $this->$assocModel->saveAll($modelData[$assocModel], array('callbacks' => false));
            $this->$assocModel->useDbConfig = 'default';
        }
    }
    
    function deleteFromFrontEnd($modelData, $site) {
        $this->useDbConfig = $site;
        $this->deleteAll(array($this->primaryKey => $modelData[$this->name][$this->primaryKey]), array('callbacks' => false));
        if (isset($this->containModels)) {
            $this->deleteAssocModels($modelData, $site);
        }
    }
    
    function deleteAssocModels($modelData, $site) {
        foreach($this->containModels as $assocModel) {
            $this->$assocModel->useDbConfig = $site;
            $this->$assocModel->deleteAll(array($this->$assocModel->primaryKey => $modelData[$this->$assocModel][$this->$assocModel->primaryKey]), array('callbacks' => false));
            $this->$assocModel->useDbConfig = 'default';
        }
    }
	
    //constructs a field list for saving models to the front end;
    //handles cases where the table schema on the front end differs from toolbox
    function getFields($site) {
        $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$this->useTable}' AND table_schema = '{$site}'";
        $fields = $this->query($query);
        $f = array();
        foreach($fields as $field) {
            $f[] = $field['COLUMNS']['COLUMN_NAME'];
        }
        return $f;
    }
    
    function getDbName($siteId) {
	    return $this->sites[$siteId];
    }
    
    function isMultisite() {
        return (isset($this->multisite) && (Model::hasField(array('sites', 'siteId')) || isset($this->inheritsFrom)));
    }
}
?>