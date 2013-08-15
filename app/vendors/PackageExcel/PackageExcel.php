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
        // die('viewvars<pre>' . htmlentities(print_r($viewVars, true)));
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
        $as->getStyle('A1:C17')->getFill()->getStartColor()->setARGB('FFFFFFFF');
        $as->getStyle('A19:C200')->getFill()->getStartColor()->setARGB('FFFFFFFF');

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
        if ($package['isFlexPackage'] == 1) {
        	$as->getCell('B12')->setValue($package['flexNumNightsMin'] . ' / ' . $package['flexNumNightsMax']);
        } else {
        	$as->getCell('B12')->setValue('');
        }
        $as->getCell('B13')->setValue($package['numGuests']);
        if (in_array('family', $package['sites'])) {
            $as->getCell('B14')->setValue($this->viewVars['package']['PackageAgeRange']['rangeLow'] . ' - ' . $this->viewVars['package']['PackageAgeRange']['rangeHigh']);
        } else {
            $as->getCell('B14')->setValue('');
        }
        $as->getCell('B15')->setValue($this->viewVars['cc']);

        $lowPriceInfo = array_reverse(array_slice($this->viewVars['lowPrice'], 0, 9));
        $lowPrice = array();
        foreach ($lowPriceInfo as $lp) {
        	if (array_key_exists($lp['PricePoint']['pricePointId'], $lowPrice)) {
        		$lowPrice[$lp['PricePoint']['pricePointId']]['dateRanges'] .= '|' . $lp['dateRanges'];
        	} else {
        		$lowPrice[$lp['PricePoint']['pricePointId']] = $lp;
        	}
        }
 
        
        $lp_row_height = 8;
        $lp_row_offset = 29 + count($lowPrice) * $lp_row_height;
        // Remove extra rows
        for ($i = 0; $i < 100; $i++) {
            $as->removeRow($lp_row_offset);
        }

        // Low price
        $i = 0;
        while ($lp = array_pop($lowPrice)) {
            $lp_row_offset = 30 + $i * $lp_row_height;
            $as->getCell('A' . $lp_row_offset)->setValue(str_replace('<br/>',', ',$lp['dateRanges']));
            $dateLineCount = substr_count($lp['dateRanges'], '|') + 1;
            $as->getRowDimension($lp_row_offset)->setRowHeight($dateLineCount * 24);
            
            $as->getCell('A' . $lp_row_offset)->setValue(str_replace('|', "\n", $lp['dateRanges']));
            $as->getCell('B' . ($lp_row_offset + 1))->setValue($package['numNights']);
            $as->getCell('B' . ($lp_row_offset + 2))->setValue($lp['retailValue']);
            $as->getCell('C' . ($lp_row_offset + 3))->setValue(
                $lp['LoaItemRatePackageRel']['guaranteePercentRetail'] / 100.0
            );
            $as->getCell('B' . ($lp_row_offset + 4))->setValue($lp['auctionPrice']);
            $as->getCell('B' . ($lp_row_offset + 5))->setValue($lp['buyNowPrice']);
            $as->getCell('B' . ($lp_row_offset + 6))->setValue($lp['flexPricePerNight']);
            $i++;
        }

        // Booking conditions
        $as->getCell('A28')->setValue($package['termsAndConditions']);

        // Blackout Weekdays
        $as->getCell('A25')->setValue($this->viewVars['bo_weekdays']);

        // Blackout Dates
        $lp_row_offset = 22;
        foreach ($this->viewVars['blackout'] as $bod) {
            $as->insertNewRowBefore($lp_row_offset + 1);
            $as->mergeCells('A' . $lp_row_offset . ':C' . $lp_row_offset);
            $as->getCell('A' . $lp_row_offset)->setValue($bod);
            $lp_row_offset++;
        }
        $as->removeRow($lp_row_offset);

        // Inclusions
        $lp_row_offset = 18;
        $inclusions = $this->viewVars['package']['ClientLoaPackageRel'][0]['Inclusions'];
        $i = 0;
        foreach ($inclusions as $inclusion) {
            $as->getCell('A' . ($lp_row_offset + $i))->setValue($inclusion['LoaItem']['itemName']);
            $as->getCell('B' . ($lp_row_offset + $i))->setValue($inclusion['LoaItem']['itemBasePrice']);
            $as->getCell('C' . ($lp_row_offset + $i))->setValue(
                '=B' . ($lp_row_offset + $i) . '*' . $inclusion['PackageLoaItemRel']['quantity']
            );
            $i++;
            $as->insertNewRowBefore($lp_row_offset + $i);
        }
        $as->removeRow($lp_row_offset + $i);
        $as->getCell('C' . ($lp_row_offset + $i))->setValue(
            '=SUM(C' . $lp_row_offset . ':C' . ($lp_row_offset + $i - 1) . ')'
        );

    }

    public
    function dump(
        $filename = 'spreadsheet'
    ) {
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
