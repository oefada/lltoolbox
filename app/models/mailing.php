<?php
class Mailing extends AppModel {

	var $name = 'Mailing';
	var $useTable = 'mailing';
	var $primaryKey = 'mailingId';
    
    var $validate = array('mailingTypeId' => array('rule1' => array('rule' => 'numeric'),
                                                   'rule2' => array('rule' => 'notEmpty')),
                          'mailingDate' => array('rule1' => array('rule' => 'validateDayOfWeek',
                                                                  'message' => 'The date you have selected is invalid for this mailing type'),
                                                 'rule2' => array('rule' => 'validateNoDuplicates',
                                                                  'message' => 'A mailing already exists for this date and mailing type.'))
                          );
    
    var $belongsTo = array('MailingType' => array('className' => 'MailingType', 'foreignKey' => 'mailingTypeId'));
    var $hasMany = array('MailingPackageSectionRel' => array('className' => 'MailingPackageSectionRel', 'foreignKey' => 'mailingId'),
                         'MailingAdvertising' => array('className' => 'MailingAdvertising', 'foreignKey' => 'mailingId'));
    
    function validateDayOfWeek($date) {
        $mailingDate = strtotime($date['mailingDate']);
        if ($mailingDate < time()) {
            return false;
        }
        $weekday = getdate($mailingDate);
        $mailingType = $this->MailingType->findByMailingTypeId($this->data['Mailing']['mailingTypeId']);
        if ($weekday['wday'] != $mailingType['MailingType']['mailingDay']) {
            return false;
        }
        else {
            return true;
        }
    }
    
    function validateNoDuplicates($date) {
        if ($this->find('first', array('conditions' => array('Mailing.mailingDate' => $date, 'Mailing.mailingTypeId' => $this->data['Mailing']['mailingTypeId'])))) {
            return false;
        }
        else {
            return true;
        }
    }

}
?>