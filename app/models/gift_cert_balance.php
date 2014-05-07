<?php
class GiftCertBalance extends AppModel
{
    public $name = 'GiftCertBalance';
    public $useTable = 'giftCertBalance';
    public $primaryKey = 'giftCertBalanceId';

    public $belongsTo = array(
        'PromoCode' => array(
            'foreignKey' => 'promoCodeId'
        ),
        'User' => array(
            'foreignKey' => 'userId'
        )
    );

    public $validate = array(
        'amount' => array(
            'rule' => array('range', -100000, 100000),
            'message' => 'Please enter a number between -100000 and 100000.'
        )
    );

    /**
     * @return bool
     */
    public function beforeSave()
    {
        if (isset($this->data['GiftCertBalance']['promoCodeId'])) {
            // get number of trackings for promoCodeId and the balance
            $results = $this->query("SELECT balance, userId FROM giftCertBalance WHERE promoCodeId = " . $this->data['GiftCertBalance']['promoCodeId'] . " ORDER BY giftCertBalanceId DESC LIMIT 1");

            // balance
            if (!empty($results)) {
                $this->data['GiftCertBalance']['balance'] = $results[0]['giftCertBalance']['balance'] + $this->data['GiftCertBalance']['amount'];
                $this->data['GiftCertBalance']['userId'] = $results[0]['giftCertBalance']['userId'];
            } else {
                $this->data['GiftCertBalance']['balance'] = $this->data['GiftCertBalance']['amount'];
            }

            // datetime
            $this->data['GiftCertBalance']['datetime'] = date("Y-m-d H:i:s", time());
        }

        return true;
    }
}
