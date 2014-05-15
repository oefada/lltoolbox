<?php
class PgBookingAuthAttempt extends AppModel
{
    var $name = 'PgBookingAuthAttempt';
    var $useTable = 'pgBookingAuthAttempt';
    var $primaryKey = 'pgBookingAuthAttemptId';
    var $belongsTo = array(
        'PgPaymentDetailCreditCard' => array('foreignKey' => 'pgPaymentDetailCreditCardId'),
    );

    public $hasOne = array(
    );


}
