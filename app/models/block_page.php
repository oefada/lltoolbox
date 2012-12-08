<?php
class BlockPage extends AppModel
{

	var $name = 'BlockPage';
	var $useTable = 'blockPages';
	var $primaryKey = 'blockPageId';
	var $displayField = 'url';
	var $order = 'url';
	var $hasMany = array('BlockRevision' => array(
			'className' => 'BlockRevision',
			'foreignKey' => 'blockPageId',
		));
	var $validate = array(
		'url' => array(
			'rule' => '/^\/.*$/',
			'message' => 'URL is a required field'
		),
		'siteId' => array(
			'rule' => 'notEmpty',
			'message' => 'siteId is required'
		),
	);

	function beforeValidate($options)
	{
		// LL only for now
		$this->data['BlockPage']['siteId'] = 1;

		if (!empty($this->data['BlockPage']['url'])) {
			$this->data['BlockPage']['url'] = BlockPageModule::filterURL($this->data['BlockPage']['url']);
		} else {
			return false;
		}
		return true;
	}

}
