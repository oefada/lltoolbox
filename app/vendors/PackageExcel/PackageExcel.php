<?php

App::import(
    'Vendor',
    'PHPExcel',
    array('file' => 'consolidated_report' . DS . 'PHPExcel-1.7.6' . DS . 'PHPExcel.php')
);
App::import(
    'Vendor',
    'PHPExcel',
    array('file' => 'consolidated_report' . DS . 'PHPExcel-1.7.6' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php')
);
App::import(
    'Vendor',
    'PHPExcel',
    array('file' => 'consolidated_report' . DS . 'PHPExcel-1.7.6' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php')
);

class PackageExcel
{

    function __construct()
    {
        // $xls = ROOT . DS . APP_DIR . DS . 'views' . DS . 'packages' . DS . 'xls' . DS . 'Package.xlsx';
        // $objReader = new PHPExcel_Reader_Excel2007();
        // $phpExcel = $objReader->load($xls);

    }

}
