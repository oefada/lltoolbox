<?php
/*
 $handle=fopen('php://output','a');
 fputcsv($handle , array_keys(current($aging)));
 foreach ($aging as $row) {
 fputcsv($handle , $row);
 }
 */

$handle = fopen('php://output' , 'a');

if (empty($promoCodeRels)){//no records

    fputcsv($handle, array('Empty RecordSet'));

}else{//there are records

    fputcsv($handle, array('PromoCodeId','PromoCode','Inactive'));//get column names.

    foreach ($promoCodeRels as $k => $row) {

        fputcsv($handle, array(formatCSV($row['PromoCodeRel']['promoCodeId']),formatCSV($row['PromoCode']['promoCode']),formatCSV($row['PromoCode']['inactive'])));//get column names.
    }
}


function formatCSV($s) {
    return str_replace("\n" , chr(10) , str_replace("\r" , '' , trim($s)));
}

