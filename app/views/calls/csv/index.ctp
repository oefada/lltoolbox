<?php
Configure::write('debug', '0'); //turn debug off or it could appear in CSV
$handle = fopen('php://output' , 'a');

function formatCSV($input){
    if (!is_array($input)) {
        return str_replace("\n" , chr(10) , str_replace("\r" , '' , trim($input)));
    }
    $newArray = array();
    foreach ($input as $key => $value) {
        $newArray[$key] = formatCSV($value);
    }
    return $newArray;
}

if (empty($calls)){//no records
    fputcsv($handle, array('Empty RecordSet'));
}else{

    //set headings
    fputcsv($handle, array_keys($calls[0]['Call']));

    foreach ($calls as $k => $row) {
        fputcsv($handle, formatCSV($row['Call']));
    }
}
?>




