<?php
require_once 'PaymentModuleInterface.php';
class NOVA implements PaymentModuleInterface
{
    public $url = 'https://www.myvirtualmerchant.com/VirtualMerchant/process.do';
    private $merchantId = 506345;
    private $userId = 'webpage';
    private $pin = 277516;

    public $map_params;
    public $post_data;
    private $valid_avs_codes = array("F", "D", "M", "P", "W", "X", "Y", "Z");

    /**
     * @param bool $test_param
     */
    public function __construct($test_param = false)
    {
        $this->post_data = array();
        $this->post_data['ssl_merchant_id'] = $this->merchantId;
        $this->post_data['ssl_user_id'] = $this->userId;
        $this->post_data['ssl_pin'] = $this->pin;
        $this->post_data['ssl_transaction_type'] = 'ccavsonly';
        $this->post_data['ssl_test_mode'] = $test_param ? 'TRUE' : 'FALSE';
        $this->post_data['ssl_result_format'] = 'ASCII';
        $this->post_data['ssl_show_form'] = 'false';
        $this->post_data['ssl_cvv2cvc2_indicator'] = '0';
        $this->post_data['ssl_salestax'] = '0';

        $this->map_params = array();
        $this->map_params['map_ticket_id'] = 'ssl_invoice_number'; // 25
        $this->map_params['map_total_amount'] = 'ssl_amount'; // 13
        $this->map_params['map_first_name'] = 'ssl_first_name'; // 20     25
        $this->map_params['map_last_name'] = 'ssl_last_name'; // 30     25
        $this->map_params['map_street'] = 'ssl_avs_address'; // 20     60
        $this->map_params['map_street2'] = 'ssl_address2'; // 30     xx
        $this->map_params['map_city'] = 'ssl_city'; // 30     30
        $this->map_params['map_state'] = 'ssl_state'; // 30     2
        $this->map_params['map_zip'] = 'ssl_avs_zip'; // 9      10
        $this->map_params['map_country'] = 'ssl_country'; // 50
        $this->map_params['map_expiration'] = 'ssl_exp_date'; // 4      2
        $this->map_params['map_card_num'] = 'ssl_card_number'; // 19
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getMappedParams()
    {
        return $this->map_params;
    }

    /**
     * @return array
     */
    public function getPostData()
    {
        return $this->post_data;
    }

    /**
     * @param $raw_response
     * @return array
     */
    public function ProcessResponse($raw_response)
    {
        $processed = array();
        $tmp_array = explode("\n", strval($raw_response));
        foreach ($tmp_array as $k => $v) {
            $tmp = explode('=', $v);
            $processed[$tmp[0]] = (isset($tmp[1])) ? $tmp[1] : 0;
        }

        $processed['avs_only'] = false;

        if ($this->post_data['ssl_transaction_type'] == 'ccavsonly') {
            $processed['avs_only'] = true;
        }

        return $processed;
    }

    /**
     * @param $cvc
     */
    public function AddCvc($cvc)
    {
        $this->post_data['ssl_cvv2cvc2'] = $cvc;
        $this->post_data['ssl_cvv2cvc2_indicator'] = '1';
    }

    /**
     * @param $response
     * @return bool
     */
    public function ChargeSuccess($response)
    {
        if (isset($response['ssl_result'])) {
            if ($response['ssl_result'] == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $response
     * @return array
     */
    public function GetMappedResponse($response)
    {
        $paymentDetail = array(
            'ppResponseDate' => date('Y-m-d H:i:s', strtotime('now')),
            'ppTransactionId' => (isset($response['ssl_txn_id'])) ? $response['ssl_txn_id'] : 0,
            'ppApprovalText' => (isset($response['ssl_result_message'])) ? $response['ssl_result_message'] : 0,
            'ppApprovalCode' => (isset($response['ssl_result'])) ? $response['ssl_result'] : 0,
            'ppAvsCode' => (isset($response['ssl_avs_response'])) ? $response['ssl_avs_response'] : 0,
            'ppCvvCode' => (isset($response['ssl_cvv2_response'])) ? $response['ssl_cvv2_response'] : 0,
            'ppResponseText' => '',
            'ppResponseSubCode' => '',
            'ppReasonCode' => '',
            'isSuccessfulCharge' => $this->ChargeSuccess($response) ? '1' : '0'
        );

        return $paymentDetail;
    }

    /**
     * @param $response
     * @param $valid_param
     * @return bool
     */
    public function IsValidResponse($response, $valid_param)
    {
        if (isset($response['ssl_invoice_number'])) {
            if (trim($response['ssl_invoice_number']) == trim($valid_param)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $response
     * @return bool
     */
    public function GetResponseTxt($response)
    {
        if (isset($response['ssl_result_message'])) {
            return $response['ssl_result_message'];
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getPostSale()
    {
        $this->post_data['ssl_transaction_type'] = 'ccsale';
        return array('ssl_transaction_type' => 'ccsale');
    }
}
