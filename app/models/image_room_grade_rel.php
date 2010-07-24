<?php
class ImageRoomGradeRel extends AppModel {
    var $name = 'ImageRoomGradeRel';
    var $useTable = 'imageRoomGradeRel';
    var $primaryKey = 'imageRoomGradeRelId';
    
    var $actsAs = array('Containable');
    
    var $belongsTo = array('Image' => array('className' => 'Image', 'foreignKey' => 'imageId'),
                           'RoomGrade' => array('className' => 'RoomGrade', 'foreignKey' => 'roomGradeId')
                           );
    
    function afterSave($created) {
        $roomGrade = $this->RoomGrade->find('first', array('conditions' => array('RoomGrade.roomGradeId' => $this->data['ImageRoomGradeRel']['roomGradeId']),
                                                           'fields' => array('RoomGrade.clientId')));
        $client = $this->RoomGrade->Client->find('first', array('conditions' => array('Client.clientId' => $roomGrade['RoomGrade']['clientId']),
                                                                'fields' => array('sites')));
        if (!empty($client)) {
            $clientSites = $client['Client']['sites'];
            $data = $this->data;
            if (empty($data['ImageRoomGradeRel']['imageRoomGradeRelId'])) {
                $data['ImageRoomGradeRel']['imageRoomGradeRelId'] = $this->id;
            }
            foreach ($clientSites as $site) {
                $this->saveToFrontEndDb($data, $site, $clientSites, false);
            }
            $this->useDbConfig = 'default';
        }
    }    
    
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
    }
    
    function deleteImageRoomGrade($imageRoomGradeRelId, $image) {
        $this->useDbConfig = 'default';
        $this->delete($imageRoomGradeRelId);
        $client = $this->RoomGrade->Client->find('first', array('conditions' => array('Client.clientId' => $image['ImageClient']['clientId']),
                                                     'fields' => array('sites')));
        if (!empty($client)) {
            $clientSites = $client['Client']['sites'];
            foreach ($clientSites as $site) {
                $data['ImageRoomGradeRel'] = $image['Image']['ImageRoomGradeRel'][0];
                $this->deleteFromFrontEnd($data, $site);
            }
            $this->useDbConfig = 'default';
        }
    }
    
}
?>