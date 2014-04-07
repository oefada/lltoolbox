<?php
class Notes extends AppModel {

	var $name = 'Notes';
	var $useTable = 'notes';
	var $primaryKey = 'notesId';
	var $displayField = 'notes';

	function beforeSave() {
		// check to make sure the promoCode does not already exist
		$this->data['PromoCode']['promoCode'] = strtoupper($this->data['PromoCode']['promoCode']);
		$result = $this->query('SELECT * FROM promoCode WHERE promoCode = "' . $this->data['PromoCode']['promoCode'] . '"');
		if (empty($result)) {
			return true;
		}
	}

    public function getNotesByPgBooking($pgBookingId)
    {
        $sql = "SELECT * FROM notes ";
        $sql .= " where pgBookingId = ".$pgBookingId;

        $result = $this->query($sql);

        return $result;
    }



    protected static function getEntityColumnsMap()
    {
        return false;
    }

}
?>
