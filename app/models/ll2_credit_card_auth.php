<?php
class Ll2CreditCardAuth extends AppModel
{
    var $name = 'Ll2CreditCardAuth';
    var $useTable = 'll2CreditCardAuth';
    var $primaryKey = 'll2CreditCardAuthId';
    var $belongsTo = array(
        'PgBookingAuthAttempt' => array('foreignKey' => 'pgBookingAuthAttemptId'),
    );

}
