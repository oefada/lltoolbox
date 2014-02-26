<?php
error_reporting(E_ALL & ~E_DEPRECATED);
App::import('Core', 'HttpSocket');

///home/html/toolbox/cake/console/cake -app /home/html/toolbox/app event_rollup

class HotelUrlCheckerShell extends Shell
{
    public $db;
    public $uses = array('Client');
    public $HttpSocket;
    public $safeHttpResponseCodes = array(200);
    public $reportHeadings = array('Client_Id', 'Name', 'URL', 'Response_Code', 'Response_Reason');
    public $deliminator = "\t";
    public $encapsulator = '"';
    public $debug = false;

    public function initialize()
    {
        global $argv;
        parent::initialize();
        $this->Client->useDbConfig = 'luxurylink';

        if (isset($this->params['help'])) {
            $this->help();
            exit(0);
        }
        if (false == $this->getUrlField()) {
            $this->out('Please enter a valid url column (e.g. url or checkRateUrl');
            exit(1);
        }

        $this->HttpSocket = new HttpSocket();
        $validStatusCodesArray = $this->getValidStatusCodesArray();
        if (false == $validStatusCodesArray) {
            $this->out('Please pass in valid status codes in the validecodes parameter');
            exit(1);
        }
        $this->safeHttpResponseCodes = $validStatusCodesArray;

        //self::log('Hotel URL Checker Job Process Started.');
        $clientData = $this->getLiveClientsWithUrls();
        if (false == $clientData) {
            // self::log('Unable to find URLs to process');
            exit(0);
        }
        $this->displayReport($clientData);
        exit(1);
    }

    public function getLiveClientsWithUrls()
    {
        $sql = "
         SELECT Client.name, " . $this->getUrlField() . ", Client.clientId
        FROM client as Client
        INNER JOIN clientType ct ON (Client.clientTypeId = ct.clientTypeId)
        WHERE Client.loaLevelId
        IN(1,2)
        AND Client.oldProductId IS NOT NULL
        AND Client.clientId <> 8455
        AND  " . $this->getUrlField() . " != ''
        AND Client.inactive !=1
      ";

        if ($this->debug == true) {
            $this->out("QUERY:\t" . $sql);
        }
        $results = $this->Client->query($sql);

        if (empty($results)) {
            return false;
        }
        return $results;
    }

    public function displayReport($clientData)
    {
        $strReportData = '';
        $strReportHeadings = '';
        $csvData =
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $this->reportHeadings, $this->deliminator, $this->encapsulator);
        foreach ($clientData as $client => $clientValue) {
            //get response

            $url = $clientValue['Client'][$this->getUrlField()];
            $httpResponse = $this->getHttpResponse(trim($url));
            //should always be an integer
            $responseCode = $httpResponse['status']['code'];
            $responseText = $httpResponse['status']['reason-phrase'];

            if (!in_array($responseCode, $this->safeHttpResponseCodes)) {
                $clientValue['Client']['ResponseCode'] = $responseCode;
                $clientValue['Client']['ResponseReason'] = $responseText;
                fputcsv(
                    $handle,
                    array(
                        $clientValue['Client']['clientId'],
                        $clientValue['Client']['name'],
                        $url,
                        $responseCode,
                        $responseText
                    ),
                    $this->deliminator,
                    $this->encapsulator
                );
            }
        }
        $contents = '';

        rewind($handle);
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        $this->out($contents);
    }

    public function getHttpResponse($url)
    {
        $socketResults = $this->HttpSocket->get($url);

        if (empty($socketResults)) {
            return false;
        }
        $response = $this->HttpSocket->response;;
        return $response;
    }

    public function __destruct()
    {
        // self::log('Hotel URL Checker Job Process Completed.');
    }


    public function get_opt()
    {
        $options = array();
        foreach ($_SERVER["argv"] as $key => $arg) {
            if (preg_match('@\-\-(.+)=(.+)@', $arg, $matches)) {
                $key = $matches[1];
                $value = $matches[2];
                $options[$key] = $value;
            } else {
                if (preg_match("@\-(.)(.)@", $arg, $matches)) {
                    $key = $matches[1];
                    $value = $matches[2];
                    $options[$key] = $value;
                }
            }
        }
        return $options;
    }

    public function getValidStatusCodesArray()
    {
        $validCodesStr = $this->params['validcodes'];
        if (!isset($validCodesStr) || empty($validCodesStr)) {
            return false;
        }
        $validCodesArray = array_map('trim', explode(",", $validCodesStr));
        return $validCodesArray;
    }

    public function getUrlField()
    {
        $urlField = $this->params['urlfield'];
        if (!isset($urlField) || empty($urlField)) {
            return false;
        }
        return trim($urlField);
    }

    public function help()
    {
        $this->out(
            "
             __   ,_ __   __       ,___
            ( /  /( /  ) ( /      /   //         /
             /  /  /--<   /      /    /_  _  _, /<  _  _
            (_,/_ /   \_(/___/  (___// /_(/_(__/ |_(/_/ (_

            by Onjefu Efada <onjefu@gmail.com>
                    "
        );
        $this->hr(false);
        $this->out("Parameters");
        $this->out("-validcodes:\ta comma seperated list of HTTP status codes that should NOT show up on report.");
        $this->out("-urlfield:\tname of url column. should be 'checkRateUrl' or 'url' ");
        $this->hr();
        $this->out("Example cron:");
        $this->out(
            "cd /Users/oefada/Development/toolbox; ./cake/console/cake hotel_url_checker -validcodes 200,300 -urlfield checkRateUrl"
        );
        $this->out("\n");
    }
}