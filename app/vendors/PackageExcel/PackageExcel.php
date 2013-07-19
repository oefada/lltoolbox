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
    private $viewVars;

    public function __construct($viewVars = null)
    {
        $this->viewVars = $viewVars;
        //die('viewvars<pre>' . htmlentities(print_r($viewVars, true)));
        $this->xls = ROOT . DS . APP_DIR . DS . 'views' . DS . 'packages' . DS . 'xls' . DS . 'Package.xlsx';
        $objReader = new PHPExcel_Reader_Excel2007();
        $this->sheet = $objReader->load($this->xls);
    }

    public function modifySheet()
    {
        $package = $this->viewVars['package']['Package'];
        $client = $this->viewVars['client'];

        // Modify Sheet
        $as = $this->sheet->getActiveSheet();

        switch ($package['siteId']) {
            case 1:
                $companyName = 'Luxury Link';
                break;
            case 2:
                $companyName = 'Family Getaway';
                break;
            default:
                $companyName = 'ERROR';
        }
        $as->getCell('A1')->setValue($companyName);

        $as->setTitle('Package ' . $package['packageId']);
        $as->getCell('C1')->setValue($package['packageId']);

        $as->getCell('A3')->setValue($client['name']);
        $as->getCell('A4')->setValue($client['locationDisplay']);
        $as->getCell('A5')->setValue($client['url']);

        $as->getCell('B7')->setValue($package['packageName']);
        $as->getCell('B8')->setValue($client['clientId']);
        $as->getCell('B9')->setValue($package['created']);
        $as->getCell('B10')->setValue($package['isBarter'] ? 'Barter' : 'Remit');
        $as->getCell('B11')->setValue($package['numNights']);
        $as->getCell('B12')->setValue($package['numGuests']);


        /*
        $as->insertNewRowBefore(3);
        $cell = $as->getCell('A3');
        $cell->setValue('This is cell A3');
        $cell = $as->getCell('B3');
        $cell->setValue('=A1');
        $as->insertNewRowBefore(1);
        */
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
