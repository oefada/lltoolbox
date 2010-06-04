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
        if (!isset($data['imageId'])) {
            $this->create();
            $this->data['imagePath'] = $data['imagePath'];
            $this->save($this->data);
            $imageId = $this->getLastInsertID();
            $data['imageId'] = $imageId;
        }
        unset($data['imagePath']);
        $this->ImageClient->create();
        $this->ImageClient->set($data);
        $this->ImageClient->save();
        return $data['imageId'];
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
    
    function getLrgSlideshow($clientId) {
        if ($slideshow = $this->ImageClient->find('all', array('conditions' => array("ImageClient.clientId = {$clientId}",
                                                                                     "ImageClient.imageTypeId = 1",
                                                                                     "ImageClient.inactive = 0",
                                                                                     "ImageClient.isHidden = 0",
                                                                                     "Image.imagePath LIKE '%lrg%'"
                                                                                    )
                                                               )
                                                  )) {
            return $slideshow;
        }
        else {
            return false;
        }
    }
    
    function inactivateLrgSlideshow($slideshow) {
        foreach($slideshow as $slide) {
            $slide['ImageClient']['inactive'] = 1;
            $this->ImageClient->create();
            $this->ImageClient->save($slide);
        }
    }
    
    
}
?>