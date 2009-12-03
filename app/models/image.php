<?php
class Image extends AppModel {

			var $name = 'Image';
			var $useTable = 'image';
			var $primaryKey = 'imageId';
			
			var $actsAs = array('Containable');
			
			var $hasMany = array('ImageClient' => array('className' => 'ImageClient', 'foreignKey' => 'imageId'));

			function getFilenamesFromDb($clientId) {
						$filenameSql = "SELECT imagePath FROM image INNER JOIN imageClient USING (imageId) WHERE clientId = {$clientId}";
						$db =& ConnectionManager::getDataSource($this->useDbConfig);
						$filenames = array();
						if ($results = mysqli_query($db->connection, $filenameSql)) {
									while($row = mysqli_fetch_assoc($results)) {
												$filenames[] = $row['imagePath'];
									}
						}
						return $filenames;
			}
			
			function createFromFile($data) {
						$this->create();
						$this->data['imagePath'] = $data['imagePath'];
						$this->save($this->data);
						$imageId = $this->getLastInsertID();
						unset($data['imagePath']);
						$this->ImageClient->create();
						$data['imageId'] = $imageId;
						$this->ImageClient->set($data);
						$this->ImageClient->save();
			}
			
			function saveCaptions($postData, $image) {
							if (!empty($postData['caption']) || !empty($image['Image']['caption'])) {
									$data['Image']['imageId'] = $image['Image']['imageId'];
									$data['Image']['caption'] = $postData['caption'];
									$data['Image']['altTag'] = $postData['caption'];
									if (empty($image['ImageClient']['caption']) || trim($image['ImageClient']['caption']) == trim($image['Image']['caption'])) {
												$data['ImageClient']['clientImageId'] = $image['ImageClient']['clientImageId'];
												$data['ImageClient']['caption'] = $data['Image']['caption'];
									}
									
									$this->create();
									$this->data = $data['Image'];
									$this->save($this->data);
									$this->ImageClient->create();
									$imgClientData = $data['ImageClient'];
									$this->ImageClient->set($imgClientData);
									$this->ImageClient->save();
						}
			}
}
?>