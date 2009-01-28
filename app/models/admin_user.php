<?php
class AdminUser extends AppModel {
    var $name = 'AdminUser';
    var $actsAs = array('Acl');
    
    var $validate = array(
        'email' => array('email'),
        'password' => array('alphaNumeric'),
        'active' => array('numeric')
    );
    
    function parentNode() {
    }
}
?>