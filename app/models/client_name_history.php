<?php
class ClientNameHistory extends AppModel {

    var $name = 'ClientNameHistory';
    var $useTable = 'clientNameHistory';
    var $primaryKey = 'clientNameHistoryId';

    var $belongsTo = array(
                        'Client' => array(
                            'className' => 'Client',
                            'foreignKey' => 'clientId'
                        ));
}
?>