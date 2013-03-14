<?php
$handle = fopen('php://output', 'a');

if (empty($promoCodeRels)){//no records

    fputcsv($handle, array('Empty RecordSet'), "\t");

}else{//there are records

    fputcsv($handle, array('PromoCodeId','PromoCode','Inactive'), "\t");//get column names.

    foreach ($promoCodeRels as $k => $row) {

        fputcsv($handle, array($row['PromoCodeRel']['promoCodeId'],$row['PromoCode']['promoCode'],$row['PromoCode']['inactive']), "\t");//get column names.
    }
}



