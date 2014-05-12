<?php
class PgPaymentDetailCreditCard extends AppModel
{
    var $name = 'PgPaymentDetailCreditCard';
    var $useTable = 'pgPaymentDetailCreditCard';
    var $primaryKey = 'pgPaymentId';
    var $belongsTo = array(
        'PgPayment' => array('foreignKey' => 'pgPaymentId'),
        'PgBookingAuthAttempt' => array('foreignKey' => 'pgBookingAuthAttemptId'),
    );

    public $hasOne = array(
    );


}
