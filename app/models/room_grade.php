<?php
class RoomGrade extends AppModel {
    var $name = 'RoomGrade';
    var $useTable = 'roomGrade';
    var $primaryKey = 'roomGradeId';
    
    var $actsAs = array('Containable');
    
    var $hasMany = array('ImageRoomGradeRel' => array('className' => 'ImageRoomGradeRel', 'foreignKey' => 'roomGradeId', 'dependent' => true),
                         'LoaItem' => array('className' => 'LoaItem', 'foreignKey' => 'roomGradeId'));
    
    var $belongsTo = array('Client' => array('className' => 'Client', 'foreignKey' => 'clientId'));
    
    var $multisite = true;
    var $containModels = array('ImageRoomGradeRel');
}
?>