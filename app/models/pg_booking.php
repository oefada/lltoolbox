<?php
class PgBooking extends AppModel
{
    var $name = 'PgBooking';
    var $useTable = 'pgBooking';
    var $primaryKey = 'pgBookingId';
    var $belongsTo = array(
        'Client' => array('foreignKey' => 'clientId'),
        'User' => array('foreignKey' => 'userId'),
    );

    public $hasMany = array(
        'PgPayment' => array(
            'foreignKey' => 'pgBookingId',
            'dependent' => true
        ),
    );

    public $hasOne = array(
    );


}