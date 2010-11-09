<?php
echo "ID,Client Name,Ticket ID,Hotel Conf. #,Guest Name,Check-in Date,Amount,Client Comments,Submitted By - Name,Submitted By - Email,Submission Date\n";

foreach ($results as $r):
	$line = array(
    $r['Invoice']['accountingInvoiceId'],
    $r['Invoice']['hotelName'],
    $r['Invoice']['ticketId'],
    $r['Invoice']['confirmationNumber'],
    $r['Invoice']['guestName'],
    $r['Invoice']['checkinDate'],
    $r['Invoice']['amount'],
    $r['Invoice']['clientComments'],
    $r['Invoice']['submittedByName'],
    $r['Invoice']['submittedByEmail'],
    $r['Invoice']['submittedByDate'] 
	); //TODO: Add Paid Search Id and Ref Url
	
	echo implode(',', $line)."\r\n";
endforeach; ?>
