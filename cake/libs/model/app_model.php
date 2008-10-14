<?php
/* SVN FILE: $Id$ */
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
 * @subpackage		cake.cake.libs.model
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Application model for Cake.
 *
 * This is a placeholder class.
 * Create the same file in app/app_model.php
 * Add your application-wide methods to the class, your models will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake.libs.model
 */
class AppModel extends Model {
	/**
	* Constructor just sets some defaults for Luxury Link and calls the parent constructor.
	*/
	function __construct($id = false, $table = null, $ds = null) {
		if ($this->name === null) {
			$this->name = get_class($this);
		}

		//only do this on models other than AppModel
		if($this->name != 'AppModel'):
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
}
?>