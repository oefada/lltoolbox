<?php
class SearchRedirect extends AppModel {

	var $name = 'SearchRedirect';
	var $useTable = 'searchRedirect';
	var $primaryKey = 'searchRedirectId';
	
	var $validate = array('keyword' => array('regex' => array('rule' => array('validateKeyword'), 'message' => 'Only lower-cased letters, numbers, and spaces are allowed'),
	                                        'isUnique' => array('rule' => array('isUnique'), 'message' => 'This keyword already exists')
	                                        ),
	                      'redirectUrl' => array('notEmpty')
	                    );
	
    var $multisite = true;

	/* This method basically duplicates Cake's Built-In alphaNumeric core rule.
	 * However, there is a problem with PHP 5.1.6 when using the built-in rule,
	 * PHP 5.1.6 is bugged and always returns 0
	 */                
	function validateKeyword() {
	   if (!preg_match("/^[a-z0-9\s]+$/", $this->data['SearchRedirect']['keyword'])) {
	       return false;
	   }
	   return true;
	}
}
?>