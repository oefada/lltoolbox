<?php
class ArticleRel extends AppModel {

	var $name = 'ArticleRel';
	var $useTable = 'articleRel';
	var $primaryKey = 'articleRelId';

	var $belongsTo = array(
						'ArticleRelType' => array('foreignKey' => 'articleRelTypeId')
					);
}
?>
