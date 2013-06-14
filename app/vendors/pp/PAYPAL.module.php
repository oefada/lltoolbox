<?php

// PAYPAL PAYMENT MODULE
// --------------------------------------------------------------------------
// To be used with Processor.class.php only.  [alee@luxurylink.com]
// Note:  See bottom of this page for sample responses

class PAYPAL
{
    public $url = 'https://payflowpro.paypal.com';
    public $map_params;
    public $post_data;

    public function PAYPAL($test_param = false)
    {

        if ($test_param) {
            $this->url = 'https://pilot-payflowpro.paypal.com';
        }

        $this->post_data = array();
        $this->post_data['USER'] = 'LuxuryLink5200';
        $this->post_data['VENDOR'] = 'LuxuryLink5200';
        $this->post_data['PARTNER'] = 'PayPal';
        $this->post_data['PWD'] = 'luxurylink00';
        $this->post_data['TENDER'] = 'C'; // C - Direct Payment using credit card
        $this->post_data['TRXTYPE'] = 'S'; // A - Authorization, S - Sale
        $this->post_data['CUSTIP'] = $_SERVER['REMOTE_ADDR'];
        $this->post_data['VERBOSITY'] = 'LOW';
        $this->post_data['CURRENCY'] = 'USD';

        $this->map_params = array();
        $this->map_params['map_ticket_id'] = 'COMMENT1';
        $this->map_params['map_total_amount'] = 'AMT';
        $this->map_params['map_first_name'] = 'FIRSTNAME';
        $this->map_params['map_last_name'] = 'LASTNAME';
        $this->map_params['map_street'] = 'STREET';
        $this->map_params['map_city'] = 'CITY';
        $this->map_params['map_state'] = 'STATE';
        $this->map_params['map_zip'] = 'ZIP';
        $this->map_params['map_country'] = 'COUNTRY';
        $this->map_params['map_expiration'] = 'EXPDATE';
        $this->map_params['map_card_num'] = 'ACCT';
    }

    public function ProcessResponse($raw_response)
    {
        $processed = array();
        $tmp_array = split('&', strval($raw_response));
        foreach ($tmp_array as $k => $v) {
            $tmp = explode('=', $v);
            $processed[$tmp[0]] = $tmp[1];
        }
        return $processed;
    }

    public function ChargeSuccess($response)
    {
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

    public function GetMappedResponse($response)
    {
        $paymentDetail = array();

        $paymentDetail['ppResponseDate'] = date('Y-m-d H:i:s', strtotime('now'));
        $paymentDetail['ppTransactionId'] = $response['PNREF'];
        $paymentDetail['ppApprovalText'] = $response['RESPMSG'];
        $paymentDetail['ppApprovalCode'] = $response['RESULT'];
        $paymentDetail['ppAvsCode'] = isset($response['AVSADDR']) ? $response['AVSADDR'] : '';
        $paymentDetail['ppResponseText'] = isset($response['PPREF']) ? $response['PPREF'] : '';
        $paymentDetail['ppResponseSubCode'] = isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '';
        $paymentDetail['ppReasonCode'] = isset($response['AUTHCODE']) ? $response['AUTHCODE'] : '';
        $paymentDetail['isSuccessfulCharge'] = ($response['RESULT'] == '0') && (stristr(
            $response['RESPMSG'],
            'APPROVED'
        )) ? 1 : 0;

        return $paymentDetail;
    }

    public function IsValidResponse($response, $valid_param)
    {
        if (isset($response['PNREF']) && isset($response['RESULT'])) {
            return true;
        } else {
            return false;
        }
    }

    public function GetResponseTxt($response)
    {
        if (isset($response['RESPMSG'])) {
            return $response['RESPMSG'];
        } else {
            return false;
        }
    }
}

/* 
----------------------------------------------------------------
SAMPLE RESPONSES -- ALL RESPONSES ARE DATATYPE (STRING)
----------------------------------------------------------------

[INVALID CARD]
RESULT=23&PNREF=VP003BA0B4E8&RESPMSG=Invalid account number

[INVALID EXP]


[DECLINED]
RESULT=12&PNREF=ESGP3AE43F2C&RESPMSG=Declined: 15005-This transaction cannot be processed.&AVSADDR=N&AVSZIP=N&CVV2MATCH=X&IAVS=N

[APPROVAL]
RESULT=0&PNREF=ELCP3D64AAFD&RESPMSG=Approved&AUTHCODE=111111&AVSADDR=N&AVSZIP=N&CVV2MATCH=X&PPREF=1G267143629758233&CORRELATIONID=e18f545947af&IAVS=N

----------------------------------------------------------------
END OF SAMPLE RESPONSES -- ALL RESPONSES ARE DATATYPE (STRING)
----------------------------------------------------------------
*/
