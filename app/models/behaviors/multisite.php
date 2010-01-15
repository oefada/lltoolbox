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

/*
CREATE TABLE `multiSite` (
  `multiSiteId` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(75) NOT NULL,
  `modelId` int(11) NOT NULL,
  `sites` set('luxurylink','family') NOT NULL,
  PRIMARY KEY (`multiSiteId`),
  KEY `model` (`model`),
  KEY `modelId` (`modelId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1
*/

class MultisiteBehavior extends ModelBehavior {
	private $databases = array('luxurylink' => 'luxurylink', 
					   'family' => 'family');
								
	private $sitesColumn = 'sites';
	
	private $propagatesTo = array();
	private $inherits = null;
	
	static private $sites = array();
	static private $currentConnection = 'default';

	function setup(&$model, $settings = array()) {
		// no special setup required	
		$this->settings[$model->name] = $settings;
		$this->model = &$model;

		if (isset($this->settings[$model->name]['propagatesTo'])) {
		    $this->propagatesTo = (array)$this->settings[$model->name]['propagatesTo'];
		}

		if (isset($this->settings[$model->name]['inherits'])) {
		    $this->inherits[$model->name] = $this->settings[$model->name]['inherits'];
		}
		
		if (isset($this->settings[$model->name]['inheritsExclusive'])) {
		    $this->inheritsExclusive[$model->name] = $this->settings[$model->name]['inheritsExclusive'];
		}
	}
	
	function beforeSave(&$model) {
	    if (empty(MultiSiteBehavior::$sites) && isset($model->data[$model->alias][$this->sitesColumn]) && !$this->inherits) {
		    MultisiteBehavior::$sites[$model->alias] = $model->data[$model->alias][$this->sitesColumn];
		}
	    return true;
	}
	
	function afterSave(&$model, $created) {
		if (!isset($model->data[$model->alias][$this->sitesColumn])) {
			$model->data[$model->alias][$this->sitesColumn] = array();
		}
	    //debug($model->alias);
	    if (!isset($model->data[$model->alias][$model->primaryKey])) {
	        $model->data[$model->alias][$model->primaryKey] = $model->id;
	    }

	    MultisiteBehavior::$sites[$model->alias] = $model->data[$model->alias][$this->sitesColumn];

	    if (!empty($model->data[$model->alias][$this->sitesColumn])) {
	        if (!is_array($model->data[$model->alias][$this->sitesColumn])) {
	            $model->data[$model->alias][$this->sitesColumn] = array($model->data[$model->alias][$this->sitesColumn]);
	        }	
	        foreach($this->databases as $database) {
				if (isset($model->deleteFirst) && $model->deleteFirst) {
					$this->deleteAllFromFrontEnd($model, $database);
				}
				if (!in_array($database, $model->data[$model->alias][$this->sitesColumn])) {
					  $modelBackup = clone($model);
					  $this->deleteFromFrontEnd($modelBackup, $database);
					  continue;
				}
				$save_model = clone($model);
				$this->unbindAssocs($save_model);
				$this->saveModel($save_model, $database);
				$db = $this->_setDb(&$model, 'default');
				if (!empty($this->settings[$model->name]['propagatesTo'])) {
						foreach($this->propagatesTo as $child) {
						   $child_model = new $child();
						   $child_model->recursive = -1;
						   if (in_array($model->primaryKey, array_keys($child_model->_schema))) {       // the parent model's primary key defines the association in the child model
							   $conditions = array('conditions' => array($model->primaryKey => $model->data[$model->alias][$model->primaryKey]));
						   }
						   else {   // the child model's primary key defines the association
							   $conditions = array('conditions' => array($child_model->primaryKey => $model->data[$model->alias][$child_model->primaryKey]));
						   }
						   $records = $child_model->find('all', $conditions);
		   
						   if (isset($child_model->deleteFirst) && $child_model->deleteFirst) {
								  $this->deleteAllFromFrontEnd($child_model, $database, $model);
						   }
						   foreach($records as $record) {
								 $child_model->create();
								 $child_model->id = $record[$child_model->alias][$child_model->primaryKey];
								 $record[$child_model->alias][$this->sitesColumn] = $model->data[$model->alias][$this->sitesColumn];
								 $child_model->set($record);
								 if (!in_array($database, $child_model->data[$child_model->alias][$this->sitesColumn])) {
									   $modelBackup = clone($child_model);
									   $this->deleteFromFrontEnd($modelBackup, $database);
									   continue;
								 }
								 $save_child = clone($child_model);
								 $this->unbindAssocs($save_child);
								 $this->saveModel($save_child, $database);
						   }
						 }
						 $this->_setDb(&$model, 'default');
				}
	       }
	    }
	}
	
	/**
	 * This method injects the search results of a model with the sites where that model has been saved.
	 * Method queries the multiSite table for the current model and returns an array of the sites this model has been saved to.
	 * This array of sites is stored in [‘ModelName’][‘site’].
	 *
	 * @param Object $model
	 * @param Array $results
	 * @param bool true if this model is the primary model searched
	 */
	function afterFind(&$model, $results, $primary) {
	    $db = ConnectionManager::getDataSource('default');
		if($primary):
			foreach($results as $k => $result):
				if (isset($result[$model->alias][$model->primaryKey])) {
					$sites = $db->query("SELECT sites FROM multiSite WHERE model = '{$model->alias}' AND modelId = {$result[$model->alias][$model->primaryKey]}");
					if (!empty($sites)){
						$results[$k][$model->alias]['sites'] = explode(',', $sites[0]['multiSite']['sites']);
					} else {
					    $results[$k][$model->alias]['sites'] = array();
					}
				}
			endforeach;
		endif;
		$this->_setDb(&$model, 'default');
		return $results;
	}
	
	  // clears out HABTM records from front end before saving them
	  function deleteAllFromFrontEnd($model, $database, $parent=null) {
		    $db = $this->_setDb(&$model, $database);
		    if ($parent) {
				$conditions = array('conditions' => array($parent->primaryKey => $parent->data[$parent->alias][$parent->primaryKey]));
		    }
		    else {
				$conditions = array('conditions' => array($model->primaryKey => $model->data[$model->alias][$model->primaryKey]));
		    }
		    $fe_records = $model->find('all', $conditions);
		    foreach ($fe_records as $fe_record) {
				$del_model = new $model();
				$del_model->create();
				$del_model->id = $fe_record[$model->alias][$model->primaryKey];
				$del_model->set($fe_record);
				$this->deleteFromFrontEnd($del_model, $database);
		    }
	  }
	
	function deleteFromFrontEnd($model, $database) {
	   $db = $this->_setDb(&$model, $database);
	   $del_model = clone($model);
	   if (!isset($this->settings[$model->name]['disableWrite']) || $this->settings[$model->name]['disableWrite'] != true) {
		    $this->unbindAssocs($del_model);
			$query = "DELETE FROM {$del_model->useTable} WHERE {$del_model->primaryKey} = {$del_model->data[$del_model->alias][$del_model->primaryKey]}";
		    $db->query($query);
	   }
	   $this->_setDb(&$model, 'default');
	}
	
	/**
	 * Method is used to store a copy of the model to be deleted so we can use it in the afterDelete
	 *
	 * @see afterDelete
	 */
	function beforeDelete(&$model, $cascade = true) {
	    $this->modelBackup = clone($model);
	}
	
	/**
	 * Method takes care of deleting a model from external databases after it's been deleted from the main toolbox database
	 */
	function afterDelete(&$model) {
	    foreach ($this->databases as $database):
	        $modelBackup = clone($this->modelBackup);
	        $db = $this->_setDb($modelBackup, $database);

	        if ($this->_tableExists($modelBackup, $db)) {
	            $db->delete($modelBackup);
	            $db2 = &ConnectionManager::getDataSource('default');

        	    $db2->query("DELETE FROM multiSite WHERE model = '{$model->alias}' and modelId = {$this->modelBackup->id}");
	            //$modelBackup->del($modelBackup->data[$modelBackup->alias][$modelBackup->primaryKey], false);
	        }
	    endforeach;
	    $this->_setDb(&$model, 'default');
	}

	/**
	 * Custom save method handles saving our model to a specified database
	 * also saves a record into multiSite table so we have a record of where this model was saaved to
	 *
	 * @param Object $model the model we are saving
	 * @param string $database the database connection key
	 */
	function saveModel($model, $database) {
	  if (!isset($this->settings[$model->name]['disableWrite']) || $this->settings[$model->name]['disableWrite'] != true) {
		$this->_setDb(&$model, $database);
		$backup = clone($model);
		$this->prepareSiteSpecificData($model, $database);
		$model->saveAll($model->data, array('validate' => false, 'callbacks' => false));
		$model = clone($backup);
	  }

	  if (empty($model->data[$model->alias][$model->primaryKey])) {
		$model->data[$model->alias][$model->primaryKey] = $model->id;
	  }
        
        $db = &ConnectionManager::getDataSource('default');
	  if (!empty($model->data[$model->alias][$this->sitesColumn])):
		    $sites = implode(',', $model->data[$model->alias][$this->sitesColumn]);
		    $db->query("REPLACE INTO multiSite (model, modelId, sites) VALUES('{$model->alias}', {$model->data[$model->alias][$model->primaryKey]}, '$sites')");
	  endif;
	}
	
	/**
	 * Wrapper method changes the database configuration for a given mode, flushing its schema and returning a datasource object.
	 *
	 * @param Object $model Model passed by reference so we can modify its database connection
	 * @param string $database The database we want to connect it to
	 *
	 * @return ConnectionManager object so we can connect to this new database directly as well as with the model
	 */
	function &_setDb(&$model, $database = 'default') {
	    $model->useDbConfig = $database;
	    $model->_schema = null;
		$db = $model->getDataSource();
		
		if($this->_tableExists($model, $db)) {
			$model->schema();
		} else {
			$model->_schema = array($model->primaryKey => array('type' => 'integer', 'null' => '', 'default' => '', 'length' => 11, 'key' => 'primary'));
		}
	    
	    return $db;
	}
	
	/**
	 * Takes the data array being saved and looks for any fields that have a corresponding field with a prepended database name.
	 * For example, in client the field blurb is saved to all databases unless another field called database_blurb is present.
	 * A field called family_blurb would overwrite the blurb for only the family database
	 *
	 * @param Object $model The model to be modified, passed by reference so it can be modified directly
	 * @param string $database The string that we use to search for any fields that are prepended with this string to use as replacements
	 */
	function prepareSiteSpecificData(&$model, $database) {
	    foreach ($model->data[$model->alias] as $col => $data) {
            if (strstr($col, $database.'_')) {
                $colArr = explode('_', $col);
                $col = $colArr[1];
            }
	        if(isset($model->data[$model->alias][$database."_".$col])) {
	            $model->data[$model->alias][$col] = $model->data[$model->alias][$database."_".$col];
	        }
	    }
	}
	
	
	function unbindAssocs(&$model) {
	     $assocs = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
	     foreach($assocs as $assoc) {
		    $models = $model->{$assoc};
		    foreach($models as $model_name => $assoc_data) {
				$model->unbindModel(array($assoc => array($model_name)), true);				
		    }
	     }
	}
	
	
    
    /**
     * Simple function to check if a table exists in a given database.
     * Used to strip a model of all associations so it doesn't try to save to associated model data to a database that doesn't need it
     * 
     * @see afterDelete
     * @see deleteExternals
     * @param Object $model Model whose table we need to check for
     * @param Object $db the database object connected to the database we need to search in
     *
     * @return bool true if table exists, false otherwise
     */
	function _tableExists($model, $db) {
	    $tableExists = $db->query("SELECT COUNT(*) as tableExists FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{$db->config['database']}' AND TABLE_NAME = '{$model->useTable}'");
	    return ($tableExists[0][0]['tableExists'] > 0);
	}
}