<?php
class AdminUser extends AppModel {
    var $name = 'AdminUser';
    var $actsAs = array('Acl');
    
    function parentNode() {
    }
}
?>