<?php

class BlockRevision extends AppModel
{

	var $name = 'BlockRevision';
	var $useTable = 'blockRevisions';
	var $primaryKey = 'blockRevisionId';
	var $displayField = 'url';
	var $order = 'blockRevisionId DESC';
	var $validate = array();
	var $belongsTo = array('BlockPage' => array(
			'className' => 'BlockPage',
			'foreignKey' => 'blockPageId',
		));

	function beforeSave($options)
	{
		if (!empty($this->data['BlockRevision']['blockData'])) {
			$this->data['BlockRevision']['sha1'] = sha1($this->data['BlockRevision']['blockData']);
		}
		return true;
	}

	function activate($blockPageId, $blockRevisionId)
	{
		$this->query('UPDATE ' . $this->useTable . ' SET `active` = (`blockRevisionId` = ?) WHERE `blockPageId` = ?;', array(
			$blockRevisionId,
			$blockPageId,
		));
	}

}
