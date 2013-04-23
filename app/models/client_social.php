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

    public $hasMany = array('ClientTwitterStat' => array('foreignKey' => 'clientId',
                                                   'conditions' => array('ClientTwitterStat.twitterUser' => 'ClientSocial.twitterUser'),
                                                   'order' => 'ClientTwitterStat.timestamp DESC'
                                                    ),
                      );

    /*
     *
     * @option is MAX or MIN
     */

    public function getTwitterStat($clientId, $twitterUser, $option ='MAX'){

        if (!isset($clientId, $twitterUser)) {
            return false;
        }
        if (isset($option)){

            switch($option){
                case 'MAX':
                    //most recent
                    $query = "select * from clientTwitterStats where timestamp = (select MAX(timestamp) from clientTwitterStats
                    WHERE clientId = ".$clientId."
            and twitterUser = '".trim($twitterUser)."')";
                break;
                case 'MIN':
                    $query = "select * from clientTwitterStats where timestamp = (select MIN(timestamp) from clientTwitterStats
                    WHERE clientId = ".$clientId."
            and twitterUser = '".trim($twitterUser)."')";
                break;

                default:
                    $query = "select * from clientTwitterStats where timestamp = (select MAX(timestamp) from clientTwitterStats
                    WHERE clientId = ".$clientId."
            and twitterUser = '".trim($twitterUser)."')";
                break;
            }
        }

        $data =   $this->query($query);

        if (!$data){

            return false;
        }else{

            return $data[0];
        }


    }

    public function getFacebookStat($clientId, $fbUrl, $option = null){

        if (!isset($clientId, $fbUrl)) {

            var_dump('pizza');
            return false;
        }

        if (isset($option)){

            switch($option){
                case 'MAX':
                    //most recent
                    $query = "select * from clientFacebookStats where timestamp = (select MAX(timestamp) from clientFacebookStats
                    WHERE clientId = ".$clientId."
                    and fbUrl = '".trim($fbUrl)."')";
                break;
                case 'MIN':
                    $query = "select * from clientFacebookStats where timestamp = (select MIN(timestamp) from clientFacebookStats
                    WHERE clientId = ".$clientId."
                    and fbUrl = '".trim($fbUrl)."')";
                break;
                default:
                    $query = "select * from clientFacebookStats where timestamp = (select MAX(timestamp) from clientFacebookStats
                    WHERE clientId = ".$clientId."
                    and fbUrl = '".trim($fbUrl)."')";
                break;
            }
        }
        $data =   $this->query($query);

        if (!$data){

            return false;
        }else{

            return $data[0];
        }
}

}
