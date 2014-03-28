<?php
/**
 * Payment Module for My Virtual Merchant (Elavon)
 * http://www.elavon.com
 *
 * Developer guide:
 * https://www.myvirtualmerchant.com/VirtualMerchant/download/developerGuide.pdf
 *
 * @author Michael Clifford <mclifford@luxurylink.com>
 */

require_once 'PaymentModuleAbstract.php';
require_once 'PaymentModuleInterface.php';
class NOVA extends PaymentModuleAbstract implements PaymentModuleInterface
{
    protected $mappedParams;
    protected $postData;
    protected $response;
    protected $url = 'https://www.myvirtualmerchant.com/VirtualMerchant/process.do';

    private $merchantId = 506345;
    private $userId = 'webpage';
    private $pin = 277516;

    /**
     * @param bool $test_param
     */
    public function __construct($test_param = false)
    {
        $this->postData = array(
            'ssl_merchant_id' => $this->merchantId,
            'ssl_user_id' => $this->userId,
            'ssl_pin' => $this->pin,
            'ssl_transaction_type' => 'ccavsonly',
            'ssl_test_mode' => $test_param ? 'TRUE' : 'FALSE',
            'ssl_result_format' => 'ASCII',
            'ssl_show_form' => 'false',
            'ssl_cvv2cvc2_indicator' => '0',
            'ssl_salestax' => '0'
        );

        $this->mappedParams = array(
            'map_ticket_id' => 'ssl_invoice_number',
            'map_total_amount' => 'ssl_amount',
            'map_first_name' => 'ssl_first_name',
            'map_last_name' => 'ssl_last_name',
            'map_street' => 'ssl_avs_address',
            'map_street2' => 'ssl_address2',
            'map_city' => 'ssl_city',
            'map_state' => 'ssl_state',
            'map_zip' => 'ssl_avs_zip',
            'map_country' => 'ssl_country',
            'map_expiration' => 'ssl_exp_date',
            'map_card_num' => 'ssl_card_number'
        );
    }

    /**
     * @param $cvc
     */
    public function addCvc($cvc)
    {
        $this->postData['ssl_cvv2cvc2'] = $cvc;
        $this->postData['ssl_cvv2cvc2_indicator'] = '1';
    }

    /**
     * @return bool
     */
    public function chargeSuccess()
    {
        $response = $this->getResponse();
        if (isset($response['ssl_result'])) {
            if ($response['ssl_result'] == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getMappedResponse()
    {
        $response = $this->getResponse();
        $paymentDetail = array(
            'ppResponseDate' => date('Y-m-d H:i:s', strtotime('now')),
            'ppTransactionId' => (isset($response['ssl_txn_id'])) ? $response['ssl_txn_id'] : 0,
            'ppApprovalText' => (isset($response['ssl_result_message'])) ? $response['ssl_result_message'] : 0,
            'ppApprovalCode' => (isset($response['ssl_result'])) ? $response['ssl_result'] : 0,
            'ppAvsCode' => (isset($response['ssl_avs_response'])) ? $response['ssl_avs_response'] : 0,
            'ppCvvCode' => (isset($response['ssl_cvv2_response'])) ? $response['ssl_cvv2_response'] : 0,
            'ppResponseText' => (isset($response['ssl_approval_code'])) ? $response['ssl_approval_code'] : '',
            'ppResponseSubcode' => '',
            'ppReasonCode' => '',
            'isSuccessfulCharge' => $this->chargeSuccess() ? 1 : 0
        );

        return $paymentDetail;
    }

    /**
     * @return array
     */
    public function getPostSale()
    {
        $this->postData['ssl_transaction_type'] = 'ccsale';
        return array('ssl_transaction_type' => 'ccsale');
    }

    /**
     * @return bool
     */
    public function getResponseTxt()
    {
        $response = $this->getResponse();
        if (isset($response['ssl_result_message'])) {
            return $response['ssl_result_message'];
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
        if (isset($response['ssl_invoice_number'])) {
            if (trim($response['ssl_invoice_number']) == trim($valid_param)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $raw_response
     * @return array
     */
    public function processResponse($raw_response)
    {
        $processed = array();
        $tmp_array = explode("\n", strval($raw_response));
        foreach ($tmp_array as $v) {
            $tmp = explode('=', $v);
            $processed[$tmp[0]] = (isset($tmp[1])) ? $tmp[1] : 0;
        }

        $processed['avs_only'] = false;

        if ($this->postData['ssl_transaction_type'] == 'ccavsonly') {
            $processed['avs_only'] = true;
        }

        $this->response = $processed;
        return $this->getResponse();
    }
}
