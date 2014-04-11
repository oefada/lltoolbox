<?php

class UserPaymentSetting extends AppModel
{

    var $name = 'UserPaymentSetting';
    var $useTable = 'userPaymentSetting';
    var $primaryKey = 'userPaymentSettingId';
    var $belongsTo = array('PaymentType' => array('foreignKey' => 'paymentTypeId'));

    var $validate = array(
        'ccNumber' => array(
            'rule' => array('cc', null, true, null),
            'message' => 'The credit card number you supplied was invalid.',
            'on' => 'create'
        ),
        'expYear' => array(
            'rule' => array('validateExpiration'),
            'message' => 'Expiration month and year must be greater than or equal to today.'
        ),
        'expMonth' => array(
            'validateExpiration' => array('rule' => array('validateExpiration'),
                'message' => 'Expiration month and year must be greater than or equal to today.'),
            'range' => array('rule' => array('range', 0, 13),
                'message' => 'Expiration month must be a number from 1 through 12')
        ),
        'address1' => array(
            'minrule' => array(
                'rule' => array('minLength', 5),
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Address must be at least 5 characters.'
            )
        )
    );


    function validateExpiration($data)
    {
        //set variables depending on what field is going through the validation rule
        if (isset($data['expYear'])) {
            $year = $data['expYear'];
            $month = $this->data['UserPaymentSetting']['expMonth'];
        } elseif (isset($data['expMonth'])) {
            $year = $this->data['UserPaymentSetting']['expYear'];
            $month = $data['expMonth'];
        }

        //test that it's a valid integer, no decimals, etc
        if ($year != floor($year) || $month != floor($month)) {
            return false;
        }

        //test that the year is not too great, max of 10 year difference
        if ($year > date('Y') + 10) {
            return false;
        }

        //test that expiration date occurs in a future month
        if (date('Y') == $year && $month >= date('n')) {
            return true;
        }

        //if the year is in the future, the month doesn't matter
        if ($year > date('Y')) {
            return true;
        }

        return false;
    }

    /**
     * @return    bool
     */
    function beforeSave()
    {
        if (
            !empty($this->data['UserPaymentSetting']['ccNumber'])
            && empty($this->data['UserPaymentSetting']['ccToken'])
        ) {
            $expMonth = str_pad($this->data['UserPaymentSetting']['expMonth'], 2, 0, STR_PAD_LEFT);
            $expYear = substr($this->data['UserPaymentSetting']['expYear'], -2);
            $expirationDate = $expMonth . $expYear;
            $datetime = date('Y-m-d H:i:s');

            $this->data['UserPaymentSetting']['ccToken'] =
                $this->tokenizeCcNum($this->data['UserPaymentSetting']['ccNumber'], $expirationDate);

            $this->data['UserPaymentSetting']['ccType'] =
                $this->getCcType($this->data['UserPaymentSetting']['ccNumber']);

            $this->data['UserPaymentSetting']['ccTokenCreated'] = $datetime;
            $this->data['UserPaymentSetting']['ccTokenModified'] = $datetime;
        }

        if (isset($this->data['UserPaymentSetting']['ccNumber'])) {
            // We no longer want to save the ccNumber
            unset($this->data['UserPaymentSetting']['ccNumber']);
        }

        return true;
    }

    /**
     * @param    mixed $results
     * @return    mixed
     */
    public function afterFind($results)
    {
        foreach ($results as $key => $val) {
            if (isset($val['UserPaymentSetting']['ccToken'])) {
                $results[$key]['UserPaymentSetting']['ccToken'] =
                    str_pad(substr($results[$key]['UserPaymentSetting']['ccToken'], -4, 4), 16, '*', STR_PAD_LEFT);
            }
        }
        return $results;
    }

    /**
     * @param    string $ccNumber
     * @param    string $expirationDate
     * @return    string
     */
    public function tokenizeCcNum($ccNumber, $expirationDate)
    {
        $tokenizer = $this->getTokenizer();
        return $tokenizer->tokenizeCC($ccNumber, $expirationDate);
    }

    /**
     * @param    string $ccToken
     * @return    string
     */
    public function detokenizeCcNum($ccToken)
    {
        $tokenizer = $this->getTokenizer();
        return $tokenizer->detokenize($ccToken);
    }

    /**
     * @param    string $ccNumber
     * @return    string
     */
    public function getCcType($ccNumber)
    {
        switch (substr($ccNumber, 0, 1)) {
            case 4:
                $ccType = 'VI';
                break;
            case 5:
                $ccType = 'MC';
                break;
            case 6:
                $ccType = 'DS';
                break;
            case 3:
                $ccType = 'AX';
                break;
            default:
                $ccType = '';
                break;
        }

        return $ccType;
    }

    /**
     * @return    TokenizerHelper
     */
    private function getTokenizer()
    {
        App::import("Vendor", "Tokenizer", array('file' => "tokenizer.php"));
        $tokenizer = new TokenizerHelper(
            TokenizerFactoryHelper::newTokenizerInstance(
                Configure::read('TokenizerService'),
                Configure::read('TokenizerOptions')
            )
        );

        return $tokenizer;
    }
}
