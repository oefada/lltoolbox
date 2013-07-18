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

    private $xls;
    public $sheet;

    public function __construct()
    {
        $this->xls = ROOT . DS . APP_DIR . DS . 'views' . DS . 'packages' . DS . 'xls' . DS . 'Package.xlsx';
        $objReader = new PHPExcel_Reader_Excel2007();
        $this->sheet = $objReader->load($this->xls);
    }

    public function dump($filename = 'spreadsheet')
    {
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Pragma: private');
        header('Cache-control: private, must-revalidate');
        header("Content-Disposition: inline; filename=$filename.xlsx");
        $writer = new PHPExcel_Writer_Excel2007($this->sheet);
        $tmpfname = tempnam("/tmp", "Ticket2553_");
        $writer->save($tmpfname);
        unset($writer);
        readfile($tmpfname);
        unlink($tmpfname);
    }

}
