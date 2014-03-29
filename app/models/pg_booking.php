<?php
class PgBooking extends AppModel
{
    var $name = 'PgBooking';
    var $useTable = 'pgBooking';
    var $primaryKey = 'pgBookingId';
    var $belongsTo = array(
        'Client' => array('foreignKey' => 'clientId'),
        'User' => array('foreignKey' => 'userId'),
        'UserPaymentSetting' => array('foreignKey' => 'userPaymentSettingId'),
        'PromoCode' => array('foreignKey' => 'promoCodeId'),
    );

    public $hasMany = array(
        'PgPayment' => array(
            'foreignKey' => 'pgBookingId',
            'dependent' => true
        ),
    );

    public $hasOne = array(
    );

	public function getStatusDisplay() {
		return array(
			 0 => 'New',
			 1 => 'Booked',
			50 => 'Canceled'
		);
	}
    public function isValidPurchaseStatus($purchaseStatus)
    {

    }

    public function dateDiff($start, $end)
    {
    $start_ts = strtotime($start);
    $end_ts = strtotime($end);
    $diff = $end_ts - $start_ts;
    return round($diff / 86400);
    }


}
