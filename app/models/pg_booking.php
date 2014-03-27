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

	public function getStatusDisplay() {
		return array(
			 0 => 'New',
			 1 => 'Booked',
			 2 => 'Processed',
			50 => 'Canceled'
		);
	}
    function getTicketPromoData($promoCodeId)
    {
        $sql = "SELECT pc.*, pcr.promoCodeId, pcr.promoId, p.promoName, p.amountOff, p.percentOff FROM promoCode pc ";
     //   $sql .= "INNER JOIN promoCode pc ON pc.promoCodeId = ptr.promoCodeId ";
        $sql .= "INNER JOIN promoCodeRel pcr ON pcr.promoCodeId = pc.promoCodeId ";
        $sql .= "INNER JOIN promo p on p.promoId = pcr.promoId ";
        $sql .= "WHERE pc.promoCodeId = ".$promoCodeId;
        $result = $this->query($sql);
        return $result;
    }
    function getClientsFromClientId($clientId)
    {
        $sql = "SELECT clientId, name, clientTypeId, parentClientId FROM client ";
        $sql .= "WHERE clientId = " . $clientId;
        $clients = $this->query($sql);
        return $clients;
    }
}
