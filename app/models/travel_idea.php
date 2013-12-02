<?php
class TravelIdea extends AppModel {

	var $name = 'TravelIdea';
	var $useTable = 'travelIdea';
	var $primaryKey = 'travelIdeaId';
	var $displayField = 'travelIdeaHeader';
	var $order = array('TravelIdea.travelIdeaHeader');
	
	var $hasMany = array('TravelIdeaItem' => array('foreignKey' => 'travelIdeaId'));
	var $belongsTo = array('LandingPage' =>array('foreignKey' => 'landingPageId'));
    
    function afterSave() {
        if (isset($this->data['TravelIdea']['siteId'])) {
            $tiId = (isset($this->data['TravelIdea']['travelIdeaId'])) ? $this->data['TravelIdea']['travelIdeaId'] : $this->id;
            $this->recursive = -1;
            $ti = $this->findByTravelIdeaId($tiId);
            $this->useDbConfig = AppModel::getDbName($this->data['TravelIdea']['siteId']);
            $this->create();
            $this->save($ti, array('callbacks' => false));
            $this->useDbConfig = 'default';
        }
    }
    
    function deleteFromFrontEnd($travelIdeaId, $siteId) {
        $this->useDbConfig = AppModel::getDbName($siteId);
        $this->del($travelIdeaId);
        $this->useDbConfig = 'default';
    }
    
}
?>
