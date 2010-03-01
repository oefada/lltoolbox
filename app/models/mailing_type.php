<?php
class MailingType extends AppModel {

	var $name = 'MailingType';
	var $useTable = 'mailingType';
	var $primaryKey = 'mailingTypeId';
    
    var $hasMany = array('Mailing' => array('className' => 'Mailing', 'foreignKey' => 'mailingTypeId'),
                         'MailingSection' => array('className' => 'MailingSection', 'foreignKey' => 'mailingTypeId'));

}
?>