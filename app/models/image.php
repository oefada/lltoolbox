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
}
?>