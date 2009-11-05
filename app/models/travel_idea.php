<?php
class TravelIdea extends AppModel {

	var $name = 'TravelIdea';
	var $useTable = 'travelIdea';
	var $primaryKey = 'travelIdeaId';
	var $displayField = 'travelIdeaHeader';
	var $order = array('TravelIdea.travelIdeaHeader');
	
	var $hasMany = array('TravelIdeaItem' => array('foreignKey' => 'travelIdeaId'));
	var $belongsTo = array('LandingPage' =>array('foreignKey' => 'landingPageId'));
    var $actsAs = array('Multisite');
}
?>
