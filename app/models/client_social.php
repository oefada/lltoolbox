<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oefada
 * Date: 4/8/13
 * Time: 6:20 PM
 * To change this template use File | Settings | File Templates.
 */
class ClientSocial extends AppModel {

    var $name = 'ClientSocial';
    var $useTable = 'clientSocial';
    var $primaryKey = 'clientSocialId';

    var $belongsTo = array('Client' => array('foreignKey' => 'clientId'));

    public $multisite = true;
}
