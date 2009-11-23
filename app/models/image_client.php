<?php
class ImageClient extends AppModel {
			
			var $name = 'ImageClient';
			var $useTable = 'imageClient';
			var $primaryKey = 'clientImageId';
			
			var $actsAs = array('Containable');
			
			var $belongsTo = array('Client' => array('className' => 'Client', 'foreignKey' => 'clientId'),
																										'Image' => array('className' => 'Image', 'foreignKey' => 'imageId'),
																										'ImageType' => array('className' => 'ImageType', 'foreignKey' => 'imageTypeId')
																										);
			
			function saveOrganizedImages($data) {
						$i = 1;
						foreach($data as $imageClientId => $image) {
												$this->create();
												$this->data['ImageClient']['clientImageId'] = $imageClientId;
												$this->data['ImageClient']['inactive'] = $image['inactive'];
												if (isset($image['imageTypeId']) && $image['imageTypeId'] == 1 && !$image['inactive']) {
															$this->data['ImageClient']['sortOrder'] = $i;
															$i++;
												}
												else {
															$this->data['ImageClient']['sortOrder'] = null;
												}												
												$this->save($this->data);
						}
			}
}
?>