<?php
class ClientSiteExtended extends AppModel {

    var $name = 'ClientSiteExtended';
	var $useTable = 'clientSiteExtended';
	var $primaryKey = 'clientSiteExtendedId';
    
    var $belongsTo = array('Client' => array('className' => 'Client', 'foreignKey' => 'clientId'));
    
    var $llFieldlist = array('longDesc', 'blurb', 'keywords', 'inactive', 'familiesShouldKnow');
    var $familyFieldlist = array('longDesc', 'blurb', 'keywords', 'inactive', 'familiesShouldKnow');
    
    function saveToFrontEnd($clientExtendedData) {
        if (!empty($clientExtendedData)) {
            $this->Client->useDbConfig = AppModel::getDbName($clientExtendedData['ClientSiteExtended']['siteId']);
            switch($clientExtendedData['ClientSiteExtended']['siteId']) {
                case 1:
                    $useFields = $this->llFieldlist;
                    break;
                case 2:
                    //$useFields = $this->familyFieldlist;
                    break;
                default:
                    return;
            }
            $setStatement = array();
            foreach ($useFields as $field) {
                if (isset($clientExtendedData['ClientSiteExtended'][$field])) {
                    $value = addslashes($clientExtendedData['ClientSiteExtended'][$field]);
                    $updateField = "{$field} = '{$value}'";
                    array_push($setStatement, $updateField);
                }
            }
            $setStatement = implode(',', $setStatement);            
            $query = "UPDATE client SET {$setStatement} WHERE clientId = {$this->Client->id}";
            $this->Client->query($query);
            $this->Client->useDbConfig = 'default';
        }
    }
    
}
?>