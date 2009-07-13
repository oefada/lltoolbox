<?php
class ClientReview extends AppModel {

	var $name = 'ClientReview';
	var $useTable = 'clientReview';
	var $primaryKey = 'clientReviewId';
	
	var $belongsTo = array('Client' => array('foreignKey' => 'clientId'), 'User' => array('foreignKey' => 'authorUserId'));
}
?>
