<?php
class ImageRoomGradeRel extends AppModel {
    var $name = 'ImageRoomGradeRel';
    var $useTable = 'imageRoomGradeRel';
    var $primaryKey = 'imageRoomGradeRelId';
    
    var $actsAs = array('Containable');
    
    var $belongsTo = array('Image' => array('className' => 'Image', 'foreignKey' => 'imageId'),
                           'RoomGrade' => array('className' => 'RoomGrade', 'foreignKey' => 'roomGradeId')
                           );
    
    function saveImageRoomGrade($roomGradeId, $image) {
        $data = array();
        $data['roomGradeId'] = $roomGradeId;
        $data['imageId'] = $image['ImageClient']['imageId'];
        if (!empty($image['Image']['ImageRoomGradeRel'])) {
            $data['imageRoomGradeRelId'] = $image['Image']['ImageRoomGradeRel'][0]['imageRoomGradeRelId'];
        }
        $this->create();
        $this->data['ImageRoomGradeRel'] = $data;
        $this->save($this->data);
        $clientSites = $this->query("SELECT sites FROM multiSite WHERE model='Client' AND modelId={$image['Client']['clientId']}");
        $sites = (is_array($clientSites[0]['multiSite']['sites'])) ? $clientSites[0]['multiSite']['sites'] : explode(',', $clientSites[0]['multiSite']['sites']);
        foreach ($sites as $site) {
            $this->recursive = -1;
            $imageRoomGradeRel = $this->find('first', array('conditions' => array('ImageRoomGradeRel.imageRoomGradeRelId' => $this->id)));
            $this->useDbConfig = $site;
            $this->create();
            $this->save($imageRoomGradeRel['ImageRoomGradeRel'], array('callbacks' => false));
        }
        $this->useDbConfig = 'default';
    }
    
    function deleteImageRoomGrade($imageRoomGradeRelId, $image) {
        $this->useDbConfig = 'default';
        $this->delete($imageRoomGradeRelId);
        $clientSites = $this->query("SELECT sites FROM multiSite WHERE model='Client' AND modelId={$image['Client']['clientId']}");
        $sites = (is_array($clientSites[0]['multiSite']['sites'])) ? $clientSites[0]['multiSite']['sites'] : explode(',', $clientSites[0]['multiSite']['sites']);
        foreach($sites as $site) {
            $this->useDbConfig = $site;
            $this->deleteAll(array('ImageRoomGradeRel.imageRoomGradeRelId' => $imageRoomGradeRelId), array('callbacks' => false));
        }
        $this->useDbConfig = 'default';
    }
    
}
?>