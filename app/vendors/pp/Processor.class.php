<?php
class Processor
{
    /**
     * @var PaymentModuleInterface module
     */
    private $module;
    private $module_list = array('AIM', 'NOVA', 'PAYPAL', 'PAYPAL_i18n');

    private $response_data = array();
    private $post_data = array();

    /**
     * @param $processor_name
     * @param bool $test_card
     * @return bool
     */
    public function __construct($processor_name, $test_card = false)
    {
        if (!in_array($processor_name, $this->module_list)) {
            return false;
        }

        require_once(APP . '/vendors/pp/' . $processor_name . '.module.php');
        $this->module = new $processor_name($test_card);
        $this->processor_name = $processor_name;
    }

    /**
     * @return PaymentModuleInterface
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param $userPaymentSetting
     * @param $ticket
     */
    public function InitPayment($userPaymentSetting, $ticket)
    {
        $ups = $userPaymentSetting['UserPaymentSetting'];
        $ups['expMonth'] = str_pad(substr($ups['expMonth'], -2, 2), 2, '0', STR_PAD_LEFT);
        $ups['expYear'] = str_pad(substr($ups['expYear'], -2, 2), 2, '0', STR_PAD_LEFT);

        $name = explode(" ", $ups['nameOnCard']);

        $firstName = $name[0];
        $lastName = $name[1];

        if (count($name) > 2) {
            $lastName = $name[(count($name) - 1)];
        }

        $db_params = array(
            'map_ticket_id' => $ticket['Ticket']['ticketId'],
            'map_total_amount' => $ticket['Ticket']['billingPrice'],
            'map_first_name' => substr($firstName, 0, 20),
            'map_last_name' => substr($lastName, 0, 20),
            'map_street' => substr(trim($ups['address1']), 0, 20),
            'map_street2' => substr(trim($ups['address2']), 0, 20),
            'map_city' => substr(trim($ups['city']), 0, 20),
            'map_state' => substr(trim($ups['state']), 0, 20),
            'map_zip' => substr(trim(str_replace(' ', '', $ups['postalCode'])), 0, 9),
            'map_country' => substr(trim($ups['country']), 0, 20),
            'map_expiration' => $ups['expMonth'] . $ups['expYear'],
            'map_card_num' => trim($ups['ccNumber'])
        );

        $this->post_data = $this->MapParams($db_params);
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
        foreach ($this->getModule()->getMappedParams() as $k => $v) {
            if (isset($params[$k])) {
                $tmp[$v] = $params[$k];
            }
        }
        return array_merge($this->getModule()->getPostData(), $tmp);
    }

    /**
     * @return string
     */
    private function SetPostFields()
    {
        $params = $this->post_data;
        $tmp_str = '';
        foreach ($params as $k => $v) {
            $tmp_str .= "$k=" . urlencode($v) . '&';
        }
        return $tmp_str;
    }

    /**
     * @return bool
     */
    public function SubmitPost()
    {
        if (!is_array($this->post_data) || !$this->post_data || empty($this->post_data)) {
            return false;
        }

        $post_string = $this->SetPostFields();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getModule()->getUrl());
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
        $this->response_data = $this->getModule()->processResponse($response);

        // If AVS only was ran, re-run as a sale
        if (
            isset($this->response_data['avs_only'])
            && $this->response_data['avs_only'] == true
            && $this->getModule()->chargeSuccess() === true
        ) {
            $this->post_data = array_merge($this->post_data, $this->getModule()->getPostSale());
            $this->SubmitPost();
        }

        $this->post_data = array();
        unset($post_string);
        unset($response);
        unset($ch);
    }
}
