<?php

$fields = array('Submission Date',
                'Processed Date',
                'Client Name',
                'Ticket ID',
                'Guest Name',
                'Check-in Date',
                'Amount',
                'Conf. #',
                'Client Comments',
                'Submitted By - Name',
                'Submitted By - Email',
                'Invoice ID');

echo implode(',', $fields)."\n";

foreach ($results as $r):
	$line = array(
    '"'.$r['Invoice']['submittedByDate'],
    " ",
    $r['Invoice']['hotelName'],
    $r['Invoice']['ticketId'],
    $r['Invoice']['guestName'],
    $r['Invoice']['checkinDate'],
    $r['Invoice']['amount'],
    $r['Invoice']['confirmationNumber'],
    $r['Invoice']['clientComments'],
    $r['Invoice']['submittedByName'],
    $r['Invoice']['submittedByEmail'],
    $r['Invoice']['accountingInvoiceId'].'"',
	); //TODO: Add Paid Search Id and Ref Url
    
	echo implode('","', $line)."\r\n";
endforeach; ?>
