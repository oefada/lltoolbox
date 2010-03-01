<?php
class MailingSection extends AppModel {

	var $name = 'MailingSection';
	var $useTable = 'mailingSection';
	var $primaryKey = 'mailingSectionId';
    
    var $belongsTo = array('MailingType' => array('className' => 'MailingType', 'foreignKey' => 'mailingTypeId'));
    var $hasMany = array('MailingPackageSectionRel' => array('className' => 'MailingPackageSectionRel', 'foreignKey' => 'mailingSectionId'));
    
    function getVariations($mailingId, $sectionId, $variationId) {
        $variations = $this->MailingPackageSectionRel->find('all', array('conditions' => array('MailingPackageSectionRel.mailingId' => $mailingId, 'MailingPackageSectionRel.mailingSectionId' => $sectionId, 'MailingPackageSectionRel.variation' => $variationId),
                                                                         'order' => array('MailingPackageSectionRel.sortOrder')));
        return $variations;
    }

}
?>