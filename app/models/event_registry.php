<?php
class EventRegistry extends AppModel {

	public $name = 'EventRegistry';
	public $useTable = 'eventRegistry';
	public $primaryKey = 'eventRegistryId';

   var $belongsTo = array(
   						'User' => array('foreignKey' => 'userId'),
   						'EventRegistryType' => array('foreignKey' => 'eventRegistryTypeID'),
					   );

	
	public function getAuctionWinnerReport($date1, $date2){
		
		$query = "  SELECT  s.siteName,
				            d.dateCreated as bookingDate,
				            d.dateCreated as paymentDate,
				            d.eventRegistryDonorId as booking,
				            r.eventRegistryId as vendorId,
				            r.registryUrl as vendor,
				            u.firstName as guestFirstName,
				            u.lastName as guestLastName,
				            d.donorAddress1 as address1,
				            d.donorAddress2 as address2,
				            d.donorCity as city,
				            d.donorStateName as state,
				            d.donorPostalCode as zip,
				            d.donorCountryCode as country,
				            u.homePhone as phone,
				            u.email as email,
				            d.ccType as ccType,
				            d.ccDigits as ccDigits,
				            '' as ccExp,
				            'Event Registry' as productType,
				            d.amount as revenue,
				            'Event Registry Donation' as auctionType,
				            0 as handlingFee
				    FROM    eventRegistry r,
				            eventRegistryDonor d,
				            user u,
				            sites s
				    WHERE   d.dateCreated BETWEEN '$date1' AND '$date2'
				    AND     d.eventRegistryId = r.eventRegistryId
				    AND     d.userId = u.userId
				    AND     r.siteId = s.siteId
				 ";
		$result = $this->query($query);
		
		return $result;
		
	}

}