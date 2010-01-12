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
        $data['imageId'] = $image['Image']['imageId'];
        if (isset($image['Image']['ImageRoomGradeRel'][0]['imageRoomGradeRelId'])) {
            $data['imageRoomGradeRelId'] = $image['Image']['ImageRoomGradeRel'][0]['imageRoomGradeRelId'];
        }
        $this->create();
        $this->data['ImageRoomGradeRel'] = $data;
        $this->save($this->data);
        $clientSites = (is_array($image['Client']['sites'])) ? $image['Client']['sites'] : explode(',', $image['Client']['sites']);
        foreach ($this->sites as $site) {
            $this->recursive = -1;
            $imageRoomGradeRel = $this->find('first', array('conditions' => array('ImageRoomGradeRel.imageRoomGradeRelId' => $this->id)));
            AppModel::saveToFrontEndDb($imageRoomGradeRel, $site, $clientSites, false);
        }
    }
    
}
?>