<?php
class PopularTravelIdea extends AppModel {

	var $name = 'PopularTravelIdea';
	var $useDbConfig = 'live';
	var $useTable = 'popularTravelIdea';
	var $primaryKey = 'popularTravelIdeaId';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Style' => array('className' => 'Style',
								'foreignKey' => 'referenceId',
								'conditions' => '',
								'fields' => 'Style.styleId, Style.styleName',
								'order' => ''
			)
	);
	
	var $validate = array('keywords' => 
										array('rule' => array('validateLinksAndKeywords'),
										'message' => 'Either Link to Styles or Keyword must be entered'),
							'linkToMultipleStyles' => array(
											array('rule' => array('custom', '/^[0-9\-]+$/i'),
													'message' => 'Must be a dash-delimited list of style ids',
													'allowEmpty' => true),
											array('rule' => array('validateLinksAndKeywords'),
													'message' => 'Either Link to Styles or Keyword must be entered')
										),
							'popularTravelIdeaName' => array('rule'=>VALID_NOT_EMPTY),
							'position' => array('rule' => array('range', 0, 7), 'message' => 'Please enter a numeric position between 1-6')
						);
						
	function validateLinksAndKeywords($data) {
		if ( ( isset($data['linkToMultipleStyles'])	&& empty($data['linkToMultipleStyles']) && empty($this->data['PopularTravelIdea']['keywords']) ) ||
		   ( isset($data['keywords']) && empty($data['keywords']) && empty($this->data['PopularTravelIdea']['linkToMultipleStyles']) ) ) {
				return false;
			} else {
				return true;
			}
	}
}
?>