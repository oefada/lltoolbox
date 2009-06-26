<?php
class Promo extends AppModel {

	var $name = 'Promo';
	var $useTable = 'promo';
	var $primaryKey = 'promoId';
	var $displayField = 'promoName';

	var $rafPromoId = 60;

	var $hasAndBelongsToMany = array(
		'PromoCode' =>
		   array('className'    => 'PromoCode',
				 'foreignKey'   => 'promoId',
				 'associationForeignKey'=> 'promoCodeId',
				 'with' => 'promoCodeRel',
				 'unique'       => true,
		   )
	);

	function checkInsertRaf($promoCodeId) {
		if (!$promoCodeId) {
			return false;
		}

		$sql = "SELECT pco.userId FROM promoCodeOwner pco ";
		$sql.= "INNER JOIN promoCodeRel pcr ON pco.promoCodeId = pcr.promoCodeId AND pcr.promoId = " . $this->rafPromoId;
		$sql.= "	WHERE pco.promoCodeId = $promoCodeId";
		$result = $this->query($sql);

		return (!empty($result)) ? $result[0]['pco']['userId'] : false;
	}

	function getRafData($promoCodeId) {
		if (!$promoCodeId) {
			return false;
		}
	
		$sql = "SELECT User.firstname, User.email, Charity.charityName FROM promoCodeOwner as PromoCodeOwner ";
		$sql.= "INNER JOIN user as User USING (userId) ";
		$sql.= "INNER JOIN charty as Charity USING (charityId) ";
		$sql.= "WHERE PromoCodeOwner.promoCodeId = $promoCodeId";
		$result = $this->query($sql);
		
		return (!empty($result)) ? $result[0] : false;
	}

	function getRafDataByTicketId($ticketId) {
		if (!$ticketId) {
			return false;
		}
		
		$sql = "SELECT User.firstname, User.email, Charity.charityName FROM ticketReferFriend as TicketReferFriend ";
		$sql.= "INNER JOIN promoCodeOwner as PromoCodeOwner ON TicketReferFriend.referrerUserId = PromoCodeOwner.userId ";
		$sql.= "INNER JOIN user as User ON PromoCodeOwner.userId = User.userId ";
		$sql.= "INNER JOIN charity as Charity ON PromoCodeOwner.charityId = Charity.charityId ";
		$sql.= "WHERE TicketReferFriend.ticketId = $ticketId";
		$result = $this->query($sql);
		
		return (!empty($result)) ? $result[0] : false;
	}
}
?>
