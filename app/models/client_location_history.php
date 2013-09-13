<?php
class ClientLocationHistory extends AppModel {

    var $name = 'ClientLocationHistory';
    var $useTable = 'clientLocationHistory';
    var $primaryKey = 'clientLocationHistoryId';

    var $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'clientId'
        ));
}
?>