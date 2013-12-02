<?php
class TravelIdeaItem extends AppModel {

	var $name = 'TravelIdeaItem';
	var $useTable = 'travelIdeaItem';
	var $primaryKey = 'travelIdeaItemId';
	var $displayField = 'travelIdeaItemName';

	var $belongsTo = array(
						'TravelIdea' => array('foreignKey' => 'travelIdeaId'),
						'TravelIdeaItemType' => array('foreignKey' => 'travelIdeaItemTypeId')
					);
    var $actsAs = array('Multisite');
}
?>
