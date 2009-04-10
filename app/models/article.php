<?php
class Article extends AppModel {

	var $name = 'Article';
	var $useTable = 'article';
	var $primaryKey = 'articleId';
	var $displayField = 'articleTitle';
	var $belongsTo = array('LandingPage' => array('foreignKey' => 'landingPageId'));

}
?>
