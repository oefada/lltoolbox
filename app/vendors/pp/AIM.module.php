<?php
/**
 * Payment Module for Authorize.net
 * http://www.authorize.net
 *
 * Developer guide:
 * http://developer.authorize.net/api/
 *
 * @author Michael Clifford <mclifford@luxurylink.com>
 */

require_once 'PaymentModuleAbstract.php';
require_once 'PaymentModuleInterface.php';
class AIM extends PaymentModuleAbstract implements PaymentModuleInterface
{
    protected $mappedParams;
    protected $postData;
    protected $response;
    protected $url = 'https://secure.authorize.net/gateway/transact.dll';

    public function __construct($test_param = false)
    {
        $this->postData = array(
            'x_version' => '3.1',
            'x_delim_data' => 'TRUE',
            'x_delim_char' => '|',
            'x_encap_char' => '',
            'x_relay_response' => 'FALSE',
            'x_login' => 'LuxuryLink5200',
            'x_tran_key' => 'zWBPWcEWgz9HPQYP',
            'x_test_request' => $test_param ? 'TRUE' : 'FALSE',
            'x_method' => 'CC',
            'x_type' => 'AUTH_CAPTURE'
        );

        $this->mappedParams = array(
            'map_ticket_id' => 'x_invoice_num',
            'map_total_amount' => 'x_amount',
            'map_first_name' => 'x_first_name',
            'map_last_name' => 'x_last_name',
            'map_street' => 'x_address',
            'map_city' => 'x_city',
            'map_state' => 'x_state',
            'map_zip' => 'x_zip',
            'map_country' => 'x_country',
            'map_expiration' => 'x_exp_date',
            'map_card_num' => 'x_card_num'
        );
    }

    public function chargeSuccess()
    {
        $response = $this->getResponse();
        if (isset($response[0])) {
            if ($response[0] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getMappedResponse()
    {
        $response = $this->getResponse();
        $paymentDetail = array(
            'ppResponseDate' => date('Y-m-d H:i:s', strtotime('now')),
            'ppTransactionId' => $response[6],
            'ppApprovalText' => $response[3],
            'ppApprovalCode' => $response[0],
            'ppAvsCode' => $response[5],
            'ppResponseText' => $response[37],
            'ppResponseSubcode' => $response[4],
            'ppReasonCode' => $response[7],
            'isSuccessfulCharge' => ($response[0] == 1) && (stristr($response[3], 'APPROVED')) ? 1 : 0
        );

        return $paymentDetail;
    }

    public function getResponseTxt()
    {
        $response = $this->getResponse();
        if (isset($response[3])) {
            return $response[3];
        } else {
            return false;
        }
    }

    public function isValidResponse($valid_param)
    {
        $response = $this->getResponse();
        if (isset($response[7])) {
            if (trim($response[7]) == trim($valid_param)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function processResponse($raw_response)
    {
        $this->response = split('[|]', strval($raw_response));
        return $this->getResponse();
    }
}
