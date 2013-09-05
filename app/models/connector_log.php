<?php
/**
 * User: oefada
 * Date: 8/2/13
 * Time: 2:53 PM
 *
 * This class is for the logging of web services.
 * It is important to log web servics for troubleshooting as you know all the parameters involved.
 */
class ConnectorLog extends AppModel
{
    public $name = 'ConnectorLog';
    public $useTable = 'connectorLog';
    public $primaryKey = 'connectorLogId';
    /*
     * possible keys are
     * requestHeaders,
     * responseHeaders,
     * request, response,
     * method,endPoint,
     * success,remoteDBId,
     * errorMsg,
     * direction,
     *
     */
    public $settings = array();

    public function setParam($key = null, $val)
    {
        if (isset($key, $val)) {
            $this->settings[$key] = $val;
        }
        return;
    }

    public function setData($strData = null)
    {
        if (isset($strData)) {
            $this->settings['request'] = $strData;
        }
        return;
    }

    public function execute()
    {
        $this->settings['requestHeaders'] = $this->getHeaders();

        if (!isset($this->settings['responseHeaders'])){
            $this->settings['responseHeaders'] = $this->getResponseHeaders();
        }

        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARTDED_FOR'] != '') {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        $this->data['ConnectorLog']['remote_address'] = abs(ip2long($ip_address));
        $this->data['ConnectorLog']['process'] = $_SERVER['REQUEST_URI'];

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->data['ConnectorLog']['referrer'] = $_SERVER['HTTP_REFERER'];
        }
        $div = "-----------------\n";
        $msg = '';
        if (isset($this->settings['errorMsg'])) {
            $msg .= $div;
            $msg .= 'ERROR MESSAGE:' . "\n";
            $msg .= $this->settings['errorMsg'] . "\n\n";
        }
        if (isset($this->settings['requestHeaders'])) {
            $msg .= $div;
            $msg .= 'REQUEST HEADERS:' . "\n";
            $msg .= $this->settings['requestHeaders'] . "\n\n";
        }
        if ($this->settings['responseHeaders']){
            $msg .= $div;
            $msg .= 'RESPONSE HEADERS:' . "\n";
            $msg .= $this->settings['responseHeaders'] . "\n\n";
        }

        if (isset($this->settings['request'])) {
            $msg .= $div;
            $msg .= 'REQUEST:' . "\n";
            $msg .= $this->settings['request'] . "\n\n";

            $this->data['ConnectorLog']['request'] = $this->settings['request'];
        }

        $this->data['ConnectorLog']['message'] = $msg;

        if (isset($this->settings['remoteDBId'])) {
            $this->data['ConnectorLog']['remoteDBId'] = $this->settings['remoteDBId'];
        }

        if (!isset($this->settings['status'])) {
            $this->data['ConnectorLog']['status'] = 1;
        } else {
            $this->data['ConnectorLog']['status'] = $this->settings['status'];
        }

        if (!empty($this->data['ConnectorLog'])) {
            //$this->create();
            $this->save($this->data);
        }
        return;
    }

    private function getHeaders()
    {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();


            $strHeaders = '';
            foreach ($headers as $header => $value) {
                if ('Cookie' !== $header) {
                    $strHeaders .= "$header: $value\n";
                }
            }
            return $strHeaders;
        }
        return false;
    }

    /*
     * Returns headers
     * for complete headers, set from web service using service functions.
     */
    private function getResponseHeaders()
    {
        if (function_exists('headers_list')){
          /* What headers are going to be sent? */
          $responseHeaders = headers_list();
            $strHeaders = '';
            foreach ($responseHeaders as $header => $value) {
                    $strHeaders .= "$header: $value\n";
            }
            return $strHeaders;
        }
        return false;
    }
}
