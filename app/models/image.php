<?php
class Image extends AppModel {

    var $name = 'Image';
    var $useTable = 'image';
    var $primaryKey = 'imageId';
    
    var $actsAs = array('Containable');
    
    var $hasMany = array('ImageClient' => array('className' => 'ImageClient', 'foreignKey' => 'imageId'),
                         'ImageRoomGradeRel' => array('className' => 'ImageRoomGradeRel', 'foreignKey' => 'imageId')
                         );
    
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
    
    function saveCaptions($postData, $image, $clientId) {
        if (!empty($postData['caption']) || !empty($image['Image']['caption'])) {
            $data['Image']['imageId'] = $image['Image']['imageId'];
            $data['Image']['caption'] = $postData['caption'];
            $data['Image']['altTag'] = $postData['caption'];
            
            $this->create();
            $this->data = $data['Image'];
            $this->save($this->data);
            $imageClients = $this->ImageClient->find('all', array('conditions' => array('ImageClient.imageId' => $data['Image']['imageId'], 'ImageClient.clientId' => $clientId)));
            foreach($imageClients as $ic) {
                if (empty($ic['ImageClient']['caption']) || trim($ic['ImageClient']['caption']) == trim($image['Image']['caption'])) {
                    $ic['ImageClient']['caption'] = $data['Image']['caption'];
                }
                $this->ImageClient->create();
                $this->ImageClient->save($ic);
            }            
        }
    }
}
?>