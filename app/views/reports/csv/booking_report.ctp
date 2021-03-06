<?php
error_reporting(0);
@ini_set('display_errors', 0);
ini_set('default_charset', 'utf-8');
//var_dump($this->params['url']['ext']);
//exit;
//create output stream
$handle = fopen('php://output', 'a');
$colNames = array(
    'Client Name',
    'Client ID',
    'Accounting ID ',
    'Loa ID',
    'Loa Account Type',
    'LOA Level',
    'Package Live date',
    'Package In Date',
    'Start Date',
    'End Date',
    'Membership Type',
    'Payment Terms',
    'Installment Types',
    'Membership Fee',
    'Membership Balance',
    'Membership Total Packages',
    'Membership Total Nights',
    'Auction % Commission',
    'BuyNow % Commission',
    'LOA Notes',
    'Num Commission Free',
    'LuxuryLink Fee',
    'Advertising Fee',
    'Risk Free Guarantee',
    'Account Executive',
    'Account Manager',
    'City',
    'State',
    'Country',
    'Packaging Notes'
);

if (!empty($results)) { //there are records
    fputcsv($handle, $colNames); //get column names.
    foreach ($results as $k => $r) {
        fputcsv(
            $handle,
            array(
                formatCSV(trim($r['Client']['name'])),
                formatCSV($r['Client']['clientId']),
                formatCSV($r['Client']['AccountingId']),
                formatCSV($r['Loa']['loaId']),
                formatCSV($r['AccountType']['accountTypeName']),
                formatCSV($r['LoaLevel']['loaLevelName']),
                formatCSV($r['Loa']['packageLiveDate'] ? date('m-d-Y', strtotime($r['Loa']['packageLiveDate'])) : ''),
                formatCSV(
                    isset($r['Loa']['customerApprovalDate']) ? date(
                        'm-d-Y',
                        strtotime($r['Loa']['customerApprovalDate'])
                    ) : ''
                ),
                formatCSV($r['Loa']['startDate'] ? date('m-d-Y', strtotime($r['Loa']['startDate'])) : ''),
                formatCSV($r['Loa']['endDate'] ? date('m-d-Y', strtotime($r['Loa']['endDate'])) : ''),
                formatCSV($r['LoaMembershipType']['loaMembershipTypeName']),
                formatCSV($r['LoaPaymentTerm']['description']),
                formatCSV($r['LoaInstallmentType']['name']),
                formatCSV($r['Loa']['membershipFee']),
                formatCSV($r['Loa']['membershipBalance']),
                formatCSV($r['Loa']['membershipTotalPackages']),
                formatCSV($r['Loa']['membershipTotalNights']),
                formatCSV($r['Loa']['auctionCommissionPerc']),
                formatCSV($r['Loa']['buynowCommissionPerc']),
                formatCSV(str_replace('"','',strip_tags(trim($r['Loa']['notes'])))),
                formatCSV($r['Loa']['loaNumberPackages']),
                formatCSV($r['Loa']['luxuryLinkFee']),
                formatCSV($r['Loa']['advertisingFee']),
                formatCSV(!empty($r['Loa']['Upgraded']) ? 'Yes' : 'No'),
                formatCSV($r['Loa']['accountExecutive']),
                formatCSV($r['Loa']['accountManager']),
                formatCSV($r['cityNew']['cityName']),
                formatCSV($r['StateNew']['StateName']),
                formatCSV($r['CountryNew']['countryName']),
                formatCSV(str_replace('"','',strip_tags(trim($r['Loa']['emailNewsletterDates'])))),
            )
        ); //get column names.
    }
} else { //there are NO records
    fputcsv($handle, array('Empty RecordSet'));
}

function formatCSV($s)
{
    return str_replace("\n", chr(10), str_replace("\r", '', trim($s)));
}

