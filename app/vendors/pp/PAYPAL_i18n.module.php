<?php
/**
 * Payment Module for Paypal Payflow Pro
 * http://www.paypal.com
 *
 * Developer guide:
 * https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pp_payflowpro_guide.pdf
 *
 * @author Michael Clifford <mclifford@luxurylink.com>
 */

require_once 'PaymentModuleAbstract.php';
require_once 'PaymentModuleInterface.php';
class PAYPAL_i18n extends PaymentModuleAbstract implements PaymentModuleInterface
{
    protected $mappedParams;
    protected $postData;
    protected $response;
    protected $url = 'https://payflowpro.paypal.com';

    /**
     * @param bool $test_param
     */
    public function __construct($test_param = false)
    {
        if ($test_param) {
            $this->url = 'https://pilot-payflowpro.paypal.com';
        }

        $this->postData = array(
            'USER' => 'lltgwebmerchant',
            'VENDOR' => 'luxurylink',
            'PARTNER' => 'PayPal',
            'PWD' => 'JreqZ2oe6KUZyCfrsa',
            'TENDER' => 'C',
            'TRXTYPE' => 'S',
            'CUSTIP' => $_SERVER['REMOTE_ADDR'],
            'VERBOSITY' => 'LOW',
            'CURRENCY' => 'GBP'
        );

        $this->mappedParams = array(
            'map_ticket_id' => 'COMMENT1',
            'map_total_amount' => 'AMT',
            'map_first_name' => 'FIRSTNAME',
            'map_last_name' => 'LASTNAME',
            'map_street' => 'STREET',
            'map_city' => 'CITY',
            'map_state' => 'STATE',
            'map_zip' => 'ZIP',
            'map_country' => 'COUNTRY',
            'map_expiration' => 'EXPDATE',
            'map_card_num' => 'ACCT'
        );
    }

    /**
     * @return bool
     */
    public function chargeSuccess()
    {
        $response = $this->getResponse();
        if (isset($response['RESULT'])) {
            if ($response['RESULT'] == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getMappedResponse()
    {
        $response = $this->getResponse();
        $paymentDetail = array(
            'ppResponseDate' => date('Y-m-d H:i:s', strtotime('now')),
            'ppTransactionId' => $response['PNREF'],
            'ppApprovalText' => $response['RESPMSG'],
            'ppApprovalCode' => $response['RESULT'],
            'ppAvsCode' => isset($response['AVSADDR']) ? $response['AVSADDR'] : '',
            'ppResponseText' => isset($response['PPREF']) ? $response['PPREF'] : '',
            'ppResponseSubcode' => isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '',
            'ppReasonCode' => isset($response['AUTHCODE']) ? $response['AUTHCODE'] : '',
            'isSuccessfulCharge' => ($response['RESULT'] == '0') && (stristr($response['RESPMSG'], 'APPROVED')) ? 1 : 0
        );

        return $paymentDetail;
    }

    /**
     * @return bool
     */
    public function getResponseTxt()
    {
        $response = $this->getResponse();
        if (isset($response['RESPMSG'])) {
            return $response['RESPMSG'];
        } else {
            return false;
        }
    }

    /**
     * @param $valid_param
     * @return bool
     */
    public function isValidResponse($valid_param)
    {
        $response = $this->getResponse();
        if (isset($response['PNREF']) && isset($response['RESULT'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $raw_response
     * @return array
     */
    public function processResponse($raw_response)
    {
        $processed = array();
        $tmp_array = explode('&', strval($raw_response));
        foreach ($tmp_array as $v) {
            $tmp = explode('=', $v);
            $processed[$tmp[0]] = $tmp[1];
        }

        $this->response = $processed;
        return $this->getResponse();
    }
}
