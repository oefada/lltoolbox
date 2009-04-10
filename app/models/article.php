<?php
class Article extends AppModel {

	var $name = 'Article';
	var $useTable = 'article';
	var $primaryKey = 'articleId';
	var $displayField = 'articleTitle';
	
	var $hasMany = array('ArticleRel' => array('foreignKey' => 'articleId'));
}
?>
