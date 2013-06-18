<?php
require(APP . '/vendors/pp/AIM.module.php');
require(APP . '/vendors/pp/PAYPAL.module.php');
require(APP . '/vendors/pp/NOVA.module.php');

class Processor
{
    public $module;
    public $module_list = array('AIM', 'NOVA', 'PAYPAL');
    public $processor_name;

    private $response_data = array();
    private $post_data = array();

    /**
     * @param $processor_name
     * @param bool $test_card
     * @return bool
     */
    public function Processor($processor_name, $test_card = false)
    {
        if (!in_array($processor_name, $this->module_list)) {
            return false;
        }
        $this->module = new $processor_name($test_card);
        $this->processor_name = $processor_name;
    }

    /**
     * @param $userPaymentSetting
     * @param $ticket
     */
    public function InitPayment($userPaymentSetting, $ticket)
    {
        // build needed parameters for a post.

        $ups = $userPaymentSetting['UserPaymentSetting'];
        $ups['expMonth'] = str_pad(substr($ups['expMonth'], -2, 2), 2, '0', STR_PAD_LEFT);
        $ups['expYear'] = str_pad(substr($ups['expYear'], -2, 2), 2, '0', STR_PAD_LEFT);

        $name = explode(" ", $ups['nameOnCard']);

        $firstName = $name[0];
        $lastName = $name[1];

        if (count($name) > 2) {
            $lastName = $name[(count($name) - 1)];
        }

        $db_params = array();
        $db_params['map_ticket_id'] = $ticket['Ticket']['ticketId'];
        $db_params['map_total_amount'] = $ticket['Ticket']['billingPrice'];
        $db_params['map_first_name'] = substr($firstName, 0, 20);
        $db_params['map_last_name'] = substr($lastName, 0, 20);
        $db_params['map_street'] = substr(trim($ups['address1']), 0, 20);
        $db_params['map_street2'] = substr(trim($ups['address2']), 0, 20);
        $db_params['map_city'] = substr(trim($ups['city']), 0, 20);
        $db_params['map_state'] = substr(trim($ups['state']), 0, 20);
        $db_params['map_zip'] = substr(trim(str_replace(' ', '', $ups['postalCode'])), 0, 9);
        $db_params['map_country'] = substr(trim($ups['country']), 0, 20);
        $db_params['map_expiration'] = $ups['expMonth'] . $ups['expYear'];
        $db_params['map_card_num'] = trim($ups['ccNumber']);

        $this->post_data = $this->MapParams($db_params);
    }

    /**
     * @return bool
     */
    public function SubmitPost()
    {
        if (!is_array($this->post_data) || !$this->post_data || empty($this->post_data)) {
            return false;
        }

        $post_string = $this->SetPostFields($this->post_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->module->url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

        $response = curl_exec($ch);
        curl_close($ch);
        $this->response_data = $this->module->ProcessResponse($response);

        // If AVS only was ran, re-run as a sale
        if (
            isset($this->response_data['avs_only'])
            && $this->response_data['avs_only'] == true
            && $this->ChargeSuccess() === true
        ) {
            $this->post_data = array_merge($this->post_data, $this->module->getPostSale());
            $this->SubmitPost();
        }

        $this->post_data = array();
        unset($post_string);
        unset($response);
        unset($ch);
    }

    /**
     * @return mixed
     */
    public function ChargeSuccess()
    {
        return $this->module->ChargeSuccess($this->response_data);
    }

    /**
     * @return mixed
     */
    public function GetResponseTxt()
    {
        return $this->module->GetResponseTxt($this->response_data);
    }

    /**
     * @return mixed
     */
    public function GetMappedResponse()
    {
        return $this->module->GetMappedResponse($this->response_data);
    }

    /**
     * @param $ticket_id
     * @return mixed
     */
    public function IsValidResponse($ticket_id)
    {
        return $this->module->IsValidResponse($this->response_data, $ticket_id);
    }

    /**
     * @param $cvc
     * @return mixed
     */
    public function AddCvc($cvc)
    {
        return $this->module->AddCvc($cvc);
    }

    /**
     * @return array
     */
    public function getResponseData()
    {
        return $this->response_data;
    }

    /**
     * @return mixed
     */
    public function dummyMappedResponse()
    {
        $paymentDetail['ppResponseDate'] = date('Y-m-d H:i:s', strtotime('now'));
        $paymentDetail['ppTransactionId'] = "DUMMY-TEST";
        $paymentDetail['ppApprovalText'] = "APPROVAL";
        $paymentDetail['ppApprovalCode'] = "0";
        $paymentDetail['ppAvsCode'] = "Y";
        $paymentDetail['ppResponseText'] = '';
        $paymentDetail['ppResponseSubCode'] = '';
        $paymentDetail['ppReasonCode'] = '';
        $paymentDetail['isSuccessfulCharge'] = 1;

        return $paymentDetail;
    }

    /**
     * @param $params
     * @return array|bool
     */
    private function MapParams($params)
    {
        if (!is_array($params)) {
            return false;
        }
        $tmp = array();
        foreach ($this->module->map_params as $k => $v) {
            if (isset($params[$k])) {
                $tmp[$v] = $params[$k];
            }
        }
        return array_merge($this->module->post_data, $tmp);
    }

    /**
     * @param $params
     * @return string
     */
    private function SetPostFields($params)
    {
        $tmp_str = '';
        foreach ($params as $k => $v) {
            $tmp_str .= "$k=" . urlencode($v) . '&';
        }
        return $tmp_str;
    }
}
