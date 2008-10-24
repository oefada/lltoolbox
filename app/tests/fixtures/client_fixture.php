<?php 
/* SVN FILE: $Id$ */
/* Client Fixture generated on: 2008-10-21 23:10:40 : 1224657520*/

class ClientFixture extends CakeTestFixture {
	var $name = 'Client';
	var $table = 'client';
	var $import = array('model' => 'Client', 'connection' => 'default');
	var $fields = array(
			'clientId' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
			'parentClientId' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'name' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'url' => array('type'=>'string', 'null' => true, 'default' => NULL),
			'email' => array('type'=>'string', 'null' => true, 'default' => NULL),
			'phone1' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 15),
			'phone2' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 15),
			'clientTypeId' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'clientLevelId' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'regionId' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'clientStatusId' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'clientAcquisitionSourceId' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'customMapLat' => array('type'=>'float', 'null' => true, 'default' => NULL),
			'customMapLong' => array('type'=>'float', 'null' => true, 'default' => NULL),
			'customMapZoomMap' => array('type'=>'float', 'null' => true, 'default' => NULL),
			'customMapZoomSat' => array('type'=>'float', 'null' => true, 'default' => NULL),
			'companyName' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 200),
			'country' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 200),
			'checkRateUrl' => array('type'=>'string', 'null' => true, 'default' => NULL),
			'numRooms' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'airportCode' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 10),
			'oldProductId' => array('type'=>'integer', 'null' => true, 'default' => NULL),
			'seoName' => array('type'=>'string', 'null' => true, 'default' => NULL),
			'indexes' => array('0' => array())
			);
	var $records = array(array(
			'clientId'  => 1,
			'parentClientId'  => 1,
			'name'  => 'Lorem ipsum dolor sit amet',
			'url'  => 'Lorem ipsum dolor sit amet',
			'email'  => 'test@test.com',
			'phone1'  => 'Lorem ipsum d',
			'phone2'  => 'Lorem ipsum d',
			'clientTypeId'  => 1,
			'clientLevelId'  => 1,
			'regionId'  => 1,
			'clientStatusId'  => 1,
			'clientAcquisitionSourceId'  => 1,
			'customMapLat'  => 1,
			'customMapLong'  => 1,
			'customMapZoomMap'  => 1,
			'customMapZoomSat'  => 1,
			'companyName'  => 'Lorem ipsum dolor sit amet',
			'country'  => 'Lorem ipsum dolor sit amet',
			'checkRateUrl'  => 'Lorem ipsum dolor sit amet',
			'numRooms'  => 1,
			'airportCode'  => 'Lorem ip',
			'oldProductId'  => 1,
			'seoName'  => 'Lorem ipsum dolor sit amet'
			));
}
?>