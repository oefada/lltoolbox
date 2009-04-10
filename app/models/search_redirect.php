<?php
class SearchRedirect extends AppModel {

	var $name = 'SearchRedirect';
	var $useTable = 'searchRedirect';
	var $primaryKey = 'searchRedirectId';
	
	var $validate = array('keyword' => array('alphaNumeric' => array('rule' => array('alphaNumeric'), 'message' => 'Only letters and numbers are allowed'),
	                                        'isUnique' => array('rule' => array('isUnique'), 'message' => 'This keyword already exists')
	                                        )
	                    );
}
?>