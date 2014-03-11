<?php
class PgPayment extends AppModel
{
    var $name = 'PgPayment';
    var $useTable = 'pgPayment';
    var $primaryKey = 'pgPaymentId';
    var $belongsTo = array(
        'PgBooking' => array('foreignKey' => 'pgBookingId'),
    );

    public $hasOne = array(
    );


}
