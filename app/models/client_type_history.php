<?php
class ClientTypeHistory extends AppModel {

    var $name = 'ClientTypeHistory';
    var $useTable = 'clientTypeHistory';
    var $primaryKey = 'clientTypeHistoryId';

    var $belongsTo = array(
                        'Client' => array(
                            'className' => 'Client',
                            'foreignKey' => 'clientId'
                        ),
                        'ClientType' => array(
                            'className' => 'ClientType',
                            'foreignKey' => 'clientTypeId'));
}
?>