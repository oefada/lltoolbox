<?php

/**
 * User: oefada
 * Date: 5/31/14
 * Time: 10:04 AM
 */
class ScreenShotter extends AppModel
{
    public $name = 'ScreenShotSaver';
    public $useTable = 'none';

    private $warnings = array();
    private $settings = array(
        'size' => 'F',
        'key' => '8652fa',
        'format' => 'PNG',
        'apiUrl' => 'http://api.screenshotmachine.com'
    );

    /**
     * We don't want to save duplicate entries and double up on notifications
     * This is also enforced at the DB level with a unique index on clientId and merchDataEntryId
     *
     * @return bool
     */
    public function beforeSave()
    {
        // return parent::beforeSave();
    }

    /**
     * @return binary
     */
    public function takeScreenShot($url)
    {
        App::import('Core', 'HttpSocket');
        $HttpSocket = new HttpSocket();
        $socketResults = $HttpSocket->get($url);


        if (!isset($socketResults)){

            return false;
        }
            $fileOutput = $HttpSocket->response['body'];
            $responseCode= $HttpSocket->response['status']['code'];
        if ($responseCode == '400'){
            return false;
        }
        return $fileOutput;
    }

    /**
     * @return array|bool
     */
    public function saveScreenShot($resultBinary, $fileName, $path = null)
    {
        if ($path == null) {
            $path == "/images/screenshot";
        }
        //Ensure that the application has the correct rights for this directory.
        if (file_put_contents($path . DS . $fileName, $resultBinary)) {
            return true;
        }
        return false;
    }

    /**
     * @param $message
     */
    public function setWarnings($message)
    {
        $this->warnings[] = $message;
    }

    public function getWarnings()
    {
        if (!empty($this->warnings)) {
            return $this->warnings;
        }
    }
}
