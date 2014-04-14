<?php
class PgBooking extends AppModel
{
    public $name = 'PgBooking';
    public $useTable = 'pgBooking';
    public $primaryKey = 'pgBookingId';
    public $belongsTo = array(
        'Client' => array('foreignKey' => 'clientId'),
        'Package' => array('foreignKey' => 'packageId'),
        'User' => array('foreignKey' => 'userId'),
        'UserPaymentSetting' => array('foreignKey' => 'userPaymentSettingId'),
        'PromoCode' => array('foreignKey' => 'promoCodeId'),
        'Country' => array(
            'foreignKey' => false,
            'conditions' => array("PgBooking.billingCountry = Country.countryId")
        )
    );

    public $hasMany = array(
        'PgPayment' => array(
            'foreignKey' => 'pgBookingId',
            'dependent' => true
        ),
    );

    public $hasOne = array();

    public function getStatusDisplay()
    {
        return array(
            0 => 'New',
            1 => 'Booked',
            50 => 'Canceled'
        );
    }

    public function isValidPurchaseStatus($purchaseStatus) { }

    public function dateDiff($start, $end)
    {
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400);
    }

    function getTicketPromoData($promoCodeId)
    {
        $sql = "SELECT pc.*, pcr.promoCodeId, pcr.promoId, p.promoName, p.amountOff, p.percentOff FROM promoCode pc ";
        $sql .= "INNER JOIN promoCodeRel pcr ON pcr.promoCodeId = pc.promoCodeId ";
        $sql .= "INNER JOIN promo p on p.promoId = pcr.promoId ";
        $sql .= "WHERE pc.promoCodeId = " . $promoCodeId;
        $result = $this->query($sql);
        return $result;
    }
}
