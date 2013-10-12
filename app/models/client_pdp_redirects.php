<?php
class ClientPdpRedirects extends AppModel {

    var $name = 'ClientPdpRedirects';
    var $useTable = 'clientPdpRedirects';
    var $primaryKey = 'clientPdpRedirectsId';

    var $belongsTo = array(
        'Client' => array(
            'className' => 'Client',
            'foreignKey' => 'clientId'
        ));
}
?>