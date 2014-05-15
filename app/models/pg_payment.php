<?php
class PgPayment extends AppModel
{
    var $name = 'PgPayment';
    var $useTable = 'pgPayment';
    var $primaryKey = 'pgPaymentId';
    var $belongsTo = array(
        'PgBooking' => array('foreignKey' => 'pgBookingId'),
        'PaymentType' => array('foreignKey' => 'paymentTypeId'),
    );
    public function readPgPayment($id){

        return $this->findByPgPaymentID($id);

    }

}
