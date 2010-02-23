<?php
class ClientSiteExtended extends AppModel {

    var $name = 'ClientSiteExtended';
	var $useTable = 'clientSiteExtended';
	var $primaryKey = 'clientSiteExtendedId';
    
    var $belongsTo = array('Client' => array('className' => 'Client', 'foreignKey' => 'clientId'));
    
    var $llFieldlist = array('clientId', 'siteId', 'longDesc', 'blurb', 'keywords', 'inactive');
    var $familyFieldlist = array('clientId', 'siteId', 'longDesc', 'blurb', 'keywords', 'inactive', 'familiesShouldKnow');
    
    function saveToFrontEnd($clientExtendedData) {
        $this->recursive = -1;
        if (!empty($clientExtendedData)) {
            $this->Client->useDbConfig = AppModel::getDbName($clientExtendedData['ClientSiteExtended']['siteId']);
            switch($clientExtendedData['ClientSiteExtended']['siteId']) {
                case 1:
                    $useFields = $this->llFieldlist;
                    break;
                case 2:
                    $useFields = $this->familyFieldlist;
                    break;
                default:
                    return;
            }
            $clientData = array();
            $clientData['Client'] = $clientExtendedData['ClientSiteExtended'];
            $clientData['Client']['modified'] = false;
            $clientData['Client']['created'] = false;
            $this->Client->create();
            $this->Client->save($clientData, array('callbacks' => false, 'fieldList' => $useFields));
            $this->Client->useDbConfig = 'default';
        }
    }
    
}
?>