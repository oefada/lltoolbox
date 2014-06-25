<?php
echo "Transaction Site,Auction ID,Ticket ID,Product Name,Public User,Remit Type,Offer Type,Date Close,Request Date,Agent,# Collected,$ Potential,$ Collected,Status\r\n";
foreach ($results as $r):
switch($r['Track']['expirationCriteriaId']) {
    case 1:
	case 4:
            $remit = 'Keep';
            break;

    case 2:
            $remit = 'Remit';
            break;

    case 3:
            $remit = 'Commission/Upgrade';
            break;
	default:
			$remit = '';
			break;
}
	$line = array(
		$siteIds[$r['Ticket']['siteId']],
	$r['Offer']['offerId'],
	$r['Ticket']['ticketId'],
	str_replace(',', '|', $r[0]['clientNames']),
	str_replace(',', '', $r['Ticket']['userFirstName'].' '.$r['Ticket']['userLastName']),
	$remit,
	$r['OfferType']['offerTypeName'],
	'',
	$r['Ticket']['created'],
	'',
	($r['Ticket']['billingPrice'] == $r[0]['moneyCollected']) ? 1 : 0,
	$r['Ticket']['billingPrice'],
	$r[0]['moneyCollected'],
	str_replace(',', $r['TicketStatus']['ticketStatusName'])
	);
	
	echo implode(',', $line)."\r\n";
endforeach; ?>