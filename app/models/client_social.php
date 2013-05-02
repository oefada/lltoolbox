<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oefada
 * Date: 4/8/13
 * Time: 6:20 PM
 * To change this template use File | Settings | File Templates.
 */
class ClientSocial extends AppModel
{

    var $name = 'ClientSocial';
    var $useTable = 'clientSocial';
    var $primaryKey = 'clientSocialId';

    var $belongsTo = array('Client' => array('foreignKey' => 'clientId'));

    public $multisite = true;

    public $hasMany = array(
        'ClientTwitterStat' => array(
            'foreignKey' => 'clientId',
            'conditions' => array('ClientTwitterStat.twitterUser' => 'ClientSocial.twitterUser'),
            'order' => 'ClientTwitterStat.timestamp DESC'
        ),
    );

    var $validate = array(

        'fbUrl' => array(
            'validateFbSiteUrl' => array(
                'rule' => array('validateFbSiteUrl'),
                'message' => 'Invalid Facebook URL, please try again.',
                'allowEmpty' => true,

            ),
        ),
        'twitterUser'=>array(
            'validateTwitterUser' => array(
                'rule' => array('validateTwitterUser'),
                'message' => 'Please use a valid Twitter Username.',
                'allowEmpty' => true,
            ),
        ),
    );

    /*
     *
     * @option is MAX or MIN
     */

    public function getTwitterStat($clientId, $twitterUser, $option = 'MAX')
    {

        if (!isset($clientId, $twitterUser)) {
            return false;
        }
        if (isset($option)) {

            switch ($option) {
                case 'MAX':
                    //most recent
                    $query = "select * from clientTwitterStats where timestamp = (select MAX(timestamp) from clientTwitterStats
                    WHERE clientId = " . $clientId . "
            and twitterUser = '" . trim($twitterUser) . "')";
                    break;
                case 'MIN':
                    $query = "select * from clientTwitterStats where timestamp = (select MIN(timestamp) from clientTwitterStats
                    WHERE clientId = " . $clientId . "
            and twitterUser = '" . trim($twitterUser) . "')";
                    break;

                default:
                    $query = "select * from clientTwitterStats where timestamp = (select MAX(timestamp) from clientTwitterStats
                    WHERE clientId = " . $clientId . "
            and twitterUser = '" . trim($twitterUser) . "')";
                    break;
            }
        }

        $data = $this->query($query);

        if (!$data) {

            return false;
        } else {

            return $data[0];
        }


    }

    public function getFacebookStat($clientId, $fbUrl, $option = null)
    {

        if (!isset($clientId, $fbUrl)) {

            return false;
        }

        if (isset($option)) {

            switch ($option) {
                case 'MAX':
                    //most recent
                    $query = "select * from clientFacebookStats where timestamp = (select MAX(timestamp) from clientFacebookStats
                    WHERE clientId = " . $clientId . "
                    and fbUrl = '" . trim($fbUrl) . "')";
                    break;
                case 'MIN':
                    $query = "select * from clientFacebookStats where timestamp = (select MIN(timestamp) from clientFacebookStats
                    WHERE clientId = " . $clientId . "
                    and fbUrl = '" . trim($fbUrl) . "')";
                    break;
                default:
                    $query = "select * from clientFacebookStats where timestamp = (select MAX(timestamp) from clientFacebookStats
                    WHERE clientId = " . $clientId . "
                    and fbUrl = '" . trim($fbUrl) . "')";
                    break;
            }
        }
        $data = $this->query($query);

        if (!$data) {

            return false;
        } else {

            return $data[0];
        }
    }

    function validateFbSiteUrl($fields)
    {

        if(empty($this->data['ClientSocial']['showFb'])){//Don't check unless set

            return TRUE;
        }
        $passed = FALSE;
        $url =$fields['fbUrl'];

        //var_dump('starting');

        if (!isset($url)) {
            $this->invalidate('fbUrl','URL not set');
            return FALSE;
        }
        App::import('Component', 'HttpRequest');
        $Httprequest = $this->HttpRequest = new HttpRequestComponent(null);

        if (!$Httprequest->isValidUrl($url)) {

            //var_dump('valid url');

            $this->invalidate('fbUrl','Invalid URL format');
            return FALSE;
        }
        if (strpos($url,'facebook.com') !== false) {

        }else{
            $this->invalidate('ClientSocial.fbUrl','Not FaceBook Domain');
            return FALSE;
        }
        $resCode = $Httprequest->check_response($url);
        if ($resCode === 200) {
//            var_dump('invalid url');
            return TRUE;
        }else {
            $this->invalidate('fbUrl','Cannot validate Facebook URL');
            return FALSE;
        }

    }

    function validateTwitterUser($fields){
        if(empty($this->data['ClientSocial']['showTw'])){//Don't check unless set
            return TRUE;
        }
        $twitterUser = $fields['twitterUser'];
        if(empty($twitterUser)){
            return FALSE;
        }

        if (strpos($twitterUser,'#') !== false) {
            return FALSE;
        }
        if (strpos($twitterUser,'@') !== false) {
            return FALSE;
        }
        $twitterUrl ='https://twitter.com/'.$twitterUser;

        App::import('Component', 'HttpRequest');
        $Httprequest = $this->HttpRequest = new HttpRequestComponent(null);

        $resCode = $Httprequest->check_response($twitterUrl);

        if ($resCode === 200) {
//            var_dump('invalid url');
            return TRUE;
        }else {
            $this->invalidate('twitterUser','Invalid Twitter User');
            return FALSE;
        }

    }
}
