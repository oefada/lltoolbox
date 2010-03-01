<?php
class MailingAdvertising extends AppModel {

    var $name = 'MailingAdvertising';
	var $useTable = 'mailingAdvertising';
	var $primaryKey = 'mailingAdvertisingId';
    
    var $belongsTo = array('Mailing' => array('className' => 'Mailing', 'foreignKey' => 'mailingId'));
    
    function saveMarketplace($data) {
        return $this->saveAll($data['MailingAdvertising']);
    }
    
    function fieldsComplete($data) {
        if (!empty($data['imageUrl']) || !empty($data['imageAlt']) || !empty($data['linkUrl']) || !empty($data['linkText']) || !empty($data['blurb'])) {
            return true;
        }
        else {
            return false;
        }
    }

}
?>