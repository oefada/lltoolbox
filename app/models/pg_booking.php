<?php
class PgBooking extends AppModel
{
    var $name = 'PgBooking';
    var $useTable = 'pgBooking';
    var $primaryKey = 'pgBookingId';
    var $belongsTo = array(
    );

    public $hasMany = array(
    );

    public $hasOne = array(
    );


}
