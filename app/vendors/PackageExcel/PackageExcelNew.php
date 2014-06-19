<?php
/**
 * Created by PhpStorm.
 * User: oefada
 * Date: 6/13/14
 * Time: 1:01 PM
 */

App::import(
    'Vendor',
    'PHPExcel',
    array('file' => 'consolidated_report' . DS . 'PHPExcel-1.7.6' . DS . 'PHPExcel.php')
);

App::import(
    'Vendor',
    'PHPExcel',
    array('file' => 'consolidated_report' . DS . 'PHPExcel-1.7.6' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php')
);
ini_set('memory_limit', '-1');
//Configure::write('debug', 0);
class PackageExcel
{

    private $xls;
    public $sheet;
    private $viewVars;
    public $defaultRowOffset= 25;
    public $current_row_offset = 0;
    private $validityPeriodArray = array();
    private $cellTotalInclusions, $rowTotalinclusions;
    private $multiDayInclusionSingleDayPrice = 0;

    public function __construct($viewVars = null)
    {
        $this->viewVars = $viewVars;
        // die('viewvars<pre>' . htmlentities(print_r($viewVars, true)));
        $this->xls = ROOT . DS . APP_DIR . DS . 'views' . DS . 'packages' . DS . 'xls' . DS . 'PackageNewB.xlsx';
        $objReader = new PHPExcel_Reader_Excel2007();
        $this->sheet = $objReader->load($this->xls);
    }

    public function modifySheet()
    {
        $package = $this->viewVars['package']['Package'];
        $client = $this->viewVars['client'];
        $roomNights = $this->viewVars['roomNights'];

        // Modify Sheet
        $as = $this->sheet->getActiveSheet();
        //$as->getStyle('A1:C16')->getFill()->getStartColor()->setARGB('FFFFFFFF');
        //$as->getStyle('A18:C200')->getFill()->getStartColor()->setARGB('FFFFFFFF');


        //$as->getCell('A1')->setValue($companyName);

        $as->setTitle('Package ' . $package['packageId']);
        $as->getCell('C1')->setValue($package['packageId']);

        $as->getCell('A3')->setValue($client['name']);
        $as->getCell('A4')->setValue($client['locationDisplay']);
        $as->getCell('A5')->setValue($client['url']);
        $as->getCell('A5')->getHyperlink()->setUrl($client['url']);
        $as->getCell('A12')->setValue('ALL PRICES IN ' .$this->viewVars['cc']);

        $as->getCell('C7')->setValue($client['clientId']);
        $as->getCell('B7')->setValue($package['isBarter'] ? 'Barter' : 'Remit');
        $as->getCell('B8')->setValue(date('M d, Y',strtotime($package['created'])));
        $as->getCell('B14')->setValue($package['packageName']);
        $as->getCell('B15')->setValue($package['roomGrade']);


        $as->getCell('B19')->setValue($package['numNights']);
        if ($package['isFlexPackage'] == 1) {
            $as->getCell('B20')->setValue($package['flexNumNightsMin'] . ' / ' . $package['flexNumNightsMax']);
        } else {
            $as->getCell('B20')->setValue('');
        }
        $as->getCell('B21')->setValue($package['numGuests']);
        $as->getCell('B22')->setValue($package['minGuests']);
        $as->getCell('B23')->setValue($package['maxAdults']);
        if ($this->viewVars['package']['PackageAgeRange']['rangeLow']) {
            $as->getCell('B24')->setValue(
                $this->viewVars['package']['PackageAgeRange']['rangeLow'] . ' - ' . $this->viewVars['package']['PackageAgeRange']['rangeHigh']
            );
        } else {
            $as->getCell('B24')->setValue('');
        }

        // Validity Periods

        $lowPriceInfo = array_reverse(array_slice($this->viewVars['lowPrice'], 0, 9));
        $lowPrice = array();
        foreach ($lowPriceInfo as $lp) {
            if (array_key_exists($lp['pricePointId'], $lowPrice)) {
                $lowPrice[$lp['pricePointId']]['dateRanges'] .= '|' . $lp['dateRanges'];
            } else {
                $lowPrice[$lp['pricePointId']] = $lp;
            }
        }
        $lowPrice2 =$lowPrice;
        $roomNightData = array();
        foreach ($roomNights as $rpKey => $rpVal) {
            $ratePeriodId = $rpVal['LoaItems'][0]['LoaItemRatePeriod']['loaItemRatePeriodId'];
            $roomNightData[$ratePeriodId] = $rpVal;
        }

        //Styling
        $invisibleFontStyleArray = array(
            'font' => array(
                'color' => array(
                    'rgb' => 'EEEEEE',
                ),
            )
        );

        $redFontStyleArray = array(
            'font' => array(
                'color' => array(
                    'rgb' => 'FF0000',
                ),
                'bold' => true,
            )
        );

        $blueFontStyleArray = array(
            'font' => array(
                'color' => array(
                    'rgb' => '0000FF',
                ),
            'italic'=>true,
            )
        );
        $boldStyleArray = array(
            'font' => array(
                'bold' => true
            )
        );
        //
        $bordersStyleArray = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'argb' => '00000000',
                    ),
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'argb' => '00000000',
                    ),
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'argb' => '00000000',
                    ),
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'argb' => '00000000',
                    ),
                ),
            )
        );
        $ratePerNightStyle = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 10,
                'bold' => true
            ),
            'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => 'FFFDA9'),
                    'color' => array('rgb' => 'FFFDA9')
                ),
        );
        $percentageStyle =  array(
            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
        );

        $currencyStyle = array('numberformat' => array('code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE));
        $currencyStyleNoCents = array('numberformat' => array('code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD));

        $as->getStyle('A1:G100')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $validity_data_row_start = $this->getDefaultOffset() + 1;

        //iterate through the validity

            $i = 1;
            $linesBeforeNextRecord= 2;

            while ($lp = array_pop($lowPrice)) {
                $thisRatePeriodIdKey = $lp['LoaItemRatePeriod']['loaItemRatePeriodId'];
//                var_dump($roomNightData[$thisRatePeriodIdKey]);
                //var_dump($roomNightData[$thisRatePeriodIdKey]['Fees']);

                $numFees = (count($roomNightData[$thisRatePeriodIdKey]['Fees']));
                //$numFees

                //height of each cell. We add 2 to accommodate rate per night and total accomodations
                $validity_record_height = $numFees + 2;

                    $lp_row_offset = $validity_data_row_start + count($lowPrice) * ($validity_record_height + $linesBeforeNextRecord);

                        //dates
                        $datesColumn = 'C';
                        $as->getCell($datesColumn . $lp_row_offset)->setValue(str_replace('<br/>', ', ', $lp['dateRanges']));
                        $dateLineCount = substr_count($lp['dateRanges'], '|') + 1;
                        $as->getRowDimension($lp_row_offset)->setRowHeight($dateLineCount * 24);
                        $as->getCell($datesColumn . $lp_row_offset)->setValue(str_replace('|', "\n", $lp['dateRanges']));
                        $as->getStyle($datesColumn . $lp_row_offset)->applyFromArray($boldStyleArray);

                        //Rate per night
                        $labelColumn = 'A';
                        $feeColumn = 'B';
                        $totalsColumn = 'D';
                        $as->getCell($labelColumn . $lp_row_offset)->setValue('Rate Per Night');
                        //$this->cellColor($labelColumn . $lp_row_offset, 'FFFDA9');
                        $as->getStyle($labelColumn . $lp_row_offset)->applyFromArray($ratePerNightStyle);

                        $as->getCell($feeColumn . $lp_row_offset)->setValue($lp['LoaItemRate']['price']);

                        $as->getStyle($feeColumn . ($lp_row_offset))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                        //multiply rate per night with number of nights
                        $as->getCell($totalsColumn . $lp_row_offset)->setValue('=PRODUCT('.$feeColumn . $lp_row_offset.',B19)');

                        $feeCounter = 0;
                        foreach ($roomNightData[$thisRatePeriodIdKey]['Fees'] as $feeKey=>$feeData){
                            $feeCounter++;
                            //list all fees
                            $feeTypeTxt = '';
                            if($feeData['Fee']['feeTypeId']== '1'){
                                $feeTypeTxt = ' (%)';
                            }
                            $rowDynamicFee = ($lp_row_offset + $feeCounter);
                            $cellDynamicFeeValue = $feeColumn . $rowDynamicFee;
                            $as->getCell($labelColumn . $rowDynamicFee)->setValue(ucwords($feeData['Fee']['feeName']). $feeTypeTxt);

                            $feeAmount = floatval($feeData['Fee']['feePercent']);
                            $as->getCell($cellDynamicFeeValue)->setValue($feeAmount);

                            //calculate totals
                            if ($feeData['Fee']['feeTypeId'] == '1') {
                                //percentage
                                if (!empty($feeAmount)) {
                                    //make sure fee is not blank, sometimes data is bad.
                                    $as->getCell($totalsColumn . ($lp_row_offset + $feeCounter))->setValue(
                                        '=PRODUCT((' . $feeColumn . $lp_row_offset . '*B19),(' . $cellDynamicFeeValue . '/100))'
                                    );
                                }else{
                                    $as->getCell($totalsColumn . ($lp_row_offset + $feeCounter))->setValue('');
                                }

                            } else {
                                //value
                                $as->getCell($totalsColumn . ($lp_row_offset + $feeCounter))->setValue(
                                    '=(PRODUCT(' . $cellDynamicFeeValue . ',B19))'
                                );
                            }
                            //align fees labels right
                            $as->getStyle($labelColumn . ($rowDynamicFee))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                            $as->getStyle($labelColumn . ($rowDynamicFee))->applyFromArray($boldStyleArray);
                            //align fees left
                            $as->getStyle($feeColumn . ($rowDynamicFee))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                            if ($feeCounter ==$numFees){
                                $lastLineIndex = ($rowDynamicFee);
                                $this->setLastLineData($i,($lastLineIndex+1));
                            }
                        }
                        //TOTAL Accommodations Label Cell
                        $rowTotalAccom = $lastLineIndex+1;
                        $cellTotalAccoLabel = 'C'.$rowTotalAccom;
                        $as->getCell($cellTotalAccoLabel)->setValue('Total Accommodations:');
                        $as->getStyle($cellTotalAccoLabel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                        //TOTAL Accommodations Formula
                        $as->getCell('D'.$rowTotalAccom)->setValue('=ROUND(SUM(D'.$lp_row_offset.':D'.($lastLineIndex).'),2)');
                        $as->getStyle('D'.$lp_row_offset.':D'.($lastLineIndex))->applyFromArray($invisibleFontStyleArray);
                        $as->getStyle('D'.$rowTotalAccom)->applyFromArray($boldStyleArray);

                        $as->getRowDimension($rowTotalAccom)->setRowHeight(18);
                        $as->getStyle('D'.$rowTotalAccom)->applyFromArray($currencyStyle);


                        //store position of total accommodations for index $i

                        //use index to draw a border around validity date groups
                        $as->getStyle('A'.$lp_row_offset.':D'.($lastLineIndex+1))->applyFromArray($bordersStyleArray);

                /*
                 *
                        if (array_key_exists($lp['LoaItemRatePeriod']['loaItemRatePeriodId'], $roomNightData)) {
                            $thisRatePeriodIdKey = $lp['LoaItemRatePeriod']['loaItemRatePeriodId'];
                            $as->getCell('B' . $lp_row_offset)->setValue(
                                $roomNightData[$thisRatePeriodIdKey]['Totals']['totalAccommodations']
                            );
                        } else {
                            $as->getCell('B' . $lp_row_offset)->setValue('');
                        }
                **/
                        //
                $i++;
            }
        // Inclusions
        $labelColumn = 'A';
        $descriptionColumn = 'B';
        $feeColumn = 'C';
        $totalsColumn = 'D';

        $inclusions_row_offset = $this->getMaxValidityRow() + 2;
        $inclusions = $this->viewVars['package']['ClientLoaPackageRel'][0]['Inclusions'];
        $numberInclusions = count($inclusions);


        foreach ($inclusions as $key=>$inclusion) {
//            var_dump($key);
            //echo $labelColumn . ($inclusions_row_offset + $key);
            if($key == 0){
                $as->getCell($labelColumn . ($inclusions_row_offset + $key))->setValue('Inclusions');
                $as->getStyle($labelColumn . ($inclusions_row_offset + $key))->applyFromArray($ratePerNightStyle);
            }
            $as->getCell($descriptionColumn . ($inclusions_row_offset + $key))->setValue($inclusion['LoaItem']['itemName']);
            $as->getCell($feeColumn . ($inclusions_row_offset + $key))->setValue($inclusion['LoaItem']['itemBasePrice']);
            $as->getCell($totalsColumn. ($inclusions_row_offset + $key))->setValue(
                '='.$feeColumn. ($inclusions_row_offset + $key) . '*' . $inclusion['PackageLoaItemRel']['quantity']
            );
            $inclusionsQty  =  floatval($inclusion['PackageLoaItemRel']['quantity']);
            if($inclusionsQty > 1){
                $this->addMultiDayInclusionSingleDayPrice($inclusion['LoaItem']['itemBasePrice']);
            }
            if(($key+1)==$numberInclusions){
                //this is the last inclusion
                $lastInclusionIndex = $key+1;

                $as->getCell($feeColumn . ($inclusions_row_offset + $lastInclusionIndex))->setValue('Total Inclusions:');
                $as->getStyle($feeColumn . ($inclusions_row_offset + $lastInclusionIndex))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                /***/
                $as->getCell($totalsColumn . ($inclusions_row_offset + $lastInclusionIndex))->setValue('=ROUND(SUM('.$totalsColumn.$inclusions_row_offset.':'.$totalsColumn.(($inclusions_row_offset + $lastInclusionIndex)-1).'),2)');
                $as->getStyle($totalsColumn . ($inclusions_row_offset + $lastInclusionIndex))->applyFromArray($currencyStyle);

                $as->getStyle($totalsColumn . ($inclusions_row_offset + $lastInclusionIndex))->applyFromArray($boldStyleArray);
                $this->rowTotalinclusions = $inclusions_row_offset + $lastInclusionIndex;
                $this->cellTotalInclusions = $totalsColumn . ($inclusions_row_offset + $lastInclusionIndex);
            }
        }
        //Valid for Travel
        $validForTravel_row_offset = $this->rowTotalinclusions + 1;
        $as->getCell($labelColumn . $validForTravel_row_offset)->setValue('Valid for Travel');
        $as->getStyle($labelColumn . ($validForTravel_row_offset))->applyFromArray($ratePerNightStyle);
        $as->getCell($descriptionColumn . $validForTravel_row_offset)->setValue('-dates-');

        $blackoutWeekdays_row_offset = $validForTravel_row_offset+ 2;
        $as->getCell($labelColumn . ($blackoutWeekdays_row_offset))->setValue('Blackout Weekdays');
        $as->getStyle($labelColumn . ($blackoutWeekdays_row_offset))->applyFromArray($ratePerNightStyle);
        $as->getCell($descriptionColumn . ($blackoutWeekdays_row_offset))->setValue($this->viewVars['bo_weekdays']);

        // Blackout Dates
        $blackout_row_offset = $blackoutWeekdays_row_offset+ 2;
        $numBlackOutDates = count($this->viewVars['blackout']);
        foreach ($this->viewVars['blackout'] as $bodKey => $bod) {
            if($bodKey == 0){
                $as->getCell($labelColumn . ($blackout_row_offset + $bodKey))->setValue('Blackout Dates');
                $as->getStyle($labelColumn . ($blackout_row_offset + $bodKey))->applyFromArray($ratePerNightStyle);
            }
            $as->getCell($descriptionColumn . ($blackout_row_offset + $bodKey))->setValue($bod.$bodKey);
        }

        //Restrictions
        $restrictions_row_offset =  $blackout_row_offset + $numBlackOutDates + 2;
        $as->getCell($labelColumn . ($restrictions_row_offset))->setValue('Restrictions, etc.');
        $as->getStyle($labelColumn . ($restrictions_row_offset))->applyFromArray($ratePerNightStyle);

        $as->getCell($descriptionColumn . ($restrictions_row_offset))->setValue($package['termsAndConditions']);

        $workSheet_row_start = $restrictions_row_offset + 2;
        $validity_record_height = 7;
        $linesBeforeNextRecord = 1;

        $i= 1;
         while ($lp = array_pop($lowPrice2)) {
            $thisRatePeriodIdKey = $lp['LoaItemRatePeriod']['loaItemRatePeriodId'];
            $lp_row_offset = $workSheet_row_start + count($lowPrice2) * ($validity_record_height + $linesBeforeNextRecord);

            $as->getCell('B' . ($lp_row_offset))->setValue('PACKAGE RETAIL VALUE IN LOCAL CURRENCY:');
            $as->getCell('B' . ($lp_row_offset +2))->setValue('Percentage of Retail:');
            $as->getCell('B' . ($lp_row_offset +4))->setValue('Percentage of Retail:');
            $as->getCell('B' . ($lp_row_offset +6))->setValue('Percentage of Daily Retail:');


             //put all in border
             $as->getStyle('B'.$lp_row_offset.':D'.($lp_row_offset +6))->applyFromArray($bordersStyleArray);

            //styling column B
            $as->getStyle('B'.$lp_row_offset)->applyFromArray($boldStyleArray);
            $as->getStyle('B'.$lp_row_offset.':B'.($lp_row_offset +6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $as->getStyle('B' . ($lp_row_offset +2))->getFont()->setItalic(true);
            $as->getStyle('B' . ($lp_row_offset +4))->getFont()->setItalic(true);
            $as->getStyle('B' . ($lp_row_offset +6))->getFont()->setItalic(true);

             //column C
             /**
             $as->getCell('C'. $lp_row_offset)->setValue(str_replace('<br/>', ', ', $lp['dateRanges']));
             $dateLineCount = substr_count($lp['dateRanges'], '|') + 1;
             $as->getRowDimension($lp_row_offset)->setRowHeight($dateLineCount * 20);
             $as->getCell('C' . $lp_row_offset)->setValue(str_replace('|', "\n", $lp['dateRanges']));
             $as->getStyle('C' . $lp_row_offset)->applyFromArray($boldStyleArray);
             **/

             $as->getCell('C' . $lp_row_offset)->setValue(str_replace('<br/>', ', ', $lp['dateRanges']));
             $dateLineCount = substr_count($lp['dateRanges'], '|') + 1;
             $as->getRowDimension($lp_row_offset)->setRowHeight($dateLineCount * 24);
             $as->getCell('C' . $lp_row_offset)->setValue(str_replace('|', "\n", $lp['dateRanges']));
             $as->getStyle('C' . $lp_row_offset)->applyFromArray($boldStyleArray);

             $retailValue = floatval($lp['retailValue']);
             $validityTotalCell = 'D'.$this->getValidityTotalRowByKey($i);

             $as->getCell('C'. ($lp_row_offset+1))->setValue('AUCTION STARTING PRICE: ');
             $as->getStyle('C'. ($lp_row_offset+1))->applyFromArray($redFontStyleArray);

             $aucPercentOfRetail =  '=('.floatval($lp['auctionPrice']).'/'.floatval($retailValue).')';
             $as->getCell('C'. ($lp_row_offset+2))->setValue($aucPercentOfRetail);
             $as->getStyle('C' .($lp_row_offset+2))->getNumberFormat()->applyFromArray($percentageStyle);
             $as->getStyle('C' .($lp_row_offset+2))->applyFromArray($blueFontStyleArray);
             $as->getCell('C'. ($lp_row_offset+3))->setValue('BUY NOW PRICE: ');
             $as->getStyle('C'. ($lp_row_offset+3))->applyFromArray($redFontStyleArray);

             $buyNowPercentOfRetail = '=('.floatval($lp['buyNowPrice']).'/'.$retailValue.')';
             $as->getCell('C'. ($lp_row_offset+4))->setValue($buyNowPercentOfRetail);
             $as->getStyle('C' .($lp_row_offset+4))->getNumberFormat()->applyFromArray($percentageStyle);
             $as->getStyle('C' .($lp_row_offset+4))->applyFromArray($blueFontStyleArray);

             $as->getCell('C'. ($lp_row_offset+5))->setValue('FLEX NIGHT PRICE (+/-): ');
             $as->getStyle('C'. ($lp_row_offset+5))->applyFromArray($redFontStyleArray);
             $flexDenominator = '(('.$validityTotalCell.'/B19)+'.$this->multiDayInclusionSingleDayPrice.')';
             $flexPercentOfRetail = '=('.floatval($lp['flexPricePerNight']).'/'.$flexDenominator.')';
             $as->getCell('C'. ($lp_row_offset+6))->setValue($flexPercentOfRetail);
             $as->getStyle('C' .($lp_row_offset+6))->getNumberFormat()->applyFromArray($percentageStyle);
             $as->getStyle('C' .($lp_row_offset+6))->applyFromArray($blueFontStyleArray);

             //Column D
             //Order is important- values must already exist.
             $totalWithInclusions = '=ROUND(('.$validityTotalCell.'+ '.$this->cellTotalInclusions.'),0)';
             $as->getCell('D'. ($lp_row_offset))->setValue($totalWithInclusions);
             $as->getStyle('D'. ($lp_row_offset))->applyFromArray($currencyStyleNoCents);
             $as->getStyle('D'. ($lp_row_offset))->applyFromArray($boldStyleArray);

             $cellTotalWithInclusions = 'D'.$lp_row_offset;
             $cellAucPercentOfRetail = 'C'.($lp_row_offset+2);
             $totalAucPercentOfRetail = '=ROUND(('.$cellAucPercentOfRetail.'* '.$cellTotalWithInclusions.'),0)';
             $as->getCell('D'. ($lp_row_offset+1))->setValue($totalAucPercentOfRetail);
             $as->getStyle('D'. ($lp_row_offset+1))->applyFromArray($currencyStyleNoCents);
             $as->getStyle('D'. ($lp_row_offset+1))->applyFromArray($redFontStyleArray);

             $cellBuyNowPercentOfRetail = 'C'.($lp_row_offset+4);
             $totalBuyNowPercentOfRetail = '=ROUND(('.$cellBuyNowPercentOfRetail.'* '.$cellTotalWithInclusions.'),0)';
             $as->getCell('D'. ($lp_row_offset+3))->setValue($totalBuyNowPercentOfRetail);
             $as->getStyle('D'. ($lp_row_offset+3))->applyFromArray($currencyStyleNoCents);
             $as->getStyle('D'. ($lp_row_offset+3))->applyFromArray($redFontStyleArray);

             $cellFlexPercentOfRetail = 'C'.($lp_row_offset+6);
             $totalFlexPercentOfRetail = '=ROUND(('.$cellFlexPercentOfRetail.'* '.$flexDenominator.'),0)';
             $as->getCell('D'. ($lp_row_offset+5))->setValue($totalFlexPercentOfRetail);
             $as->getStyle('D'. ($lp_row_offset+5))->applyFromArray($currencyStyleNoCents);
             $as->getStyle('D'. ($lp_row_offset+5))->applyFromArray($redFontStyleArray);

            $i++;
        }

//
        /**
        // Low price

        **/
////
////            $this->addToCurrentOffset($offsetAddition);
////            $this->setValidityPeriodFirstLinePosition($this->getCurrentRow());
////            $currentRow = $this->getCurrentRow();
////
////            //set validity dates
////            $as->getCell('A' . $offsetAddition)->setValue(str_replace('<br/>', ', ', $lp['dateRanges']));
//////            $dateLineCount = substr_count($lp['dateRanges'], '|') + 1;
//////            $as->getRowDimension($offsetAddition)->setRowHeight($dateLineCount * 24);
//////            $as->getCell('A' . $offsetAddition)->setValue('Validity: ' . str_replace('|', "\n", $lp['dateRanges']));
//
//            $i++;
//            break;
//        }




          /*   $lp_row_offset = 31 + $i * $lp_row_height;
            $as->getCell('A' . $lp_row_offset)->setValue(str_replace('<br/>', ', ', $lp['dateRanges']));
            $dateLineCount = substr_count($lp['dateRanges'], '|') + 1;
            $as->getRowDimension($lp_row_offset)->setRowHeight($dateLineCount * 24);

            $as->getCell('A' . $lp_row_offset)->setValue('Validity: ' . str_replace('|', "\n", $lp['dateRanges']));

            $as->getCell('B' . ($lp_row_offset + 1))->setValue($roomNights[0]['LoaItems'][0]['LoaItem']['itemName']);
            $as->getCell('B' . ($lp_row_offset + 2))->setValue($package['numNights']);
            $as->getCell('B' . ($lp_row_offset + 3))->setValue($lp['retailValue']);
            if (array_key_exists($lp['LoaItemRatePeriod']['loaItemRatePeriodId'], $roomNightData)) {
                $thisRatePeriodIdKey = $lp['LoaItemRatePeriod']['loaItemRatePeriodId'];
                $as->getCell('B' . ($lp_row_offset + 4))->setValue(
                    $roomNightData[$thisRatePeriodIdKey]['Totals']['totalAccommodations']
                );
            } else {
                $as->getCell('B' . ($lp_row_offset + 4))->setValue('');
            }
            $as->getCell('B' . ($lp_row_offset + 5))->setValue($lp['LoaItemRate']['price']);
            $as->getCell('C' . ($lp_row_offset + 6))->setValue(
                $lp['LoaItemRatePackageRel']['guaranteePercentRetail'] / 100.0
            );
            $as->getCell('B' . ($lp_row_offset + 7))->setValue($lp['auctionPrice']);
            $as->getCell('B' . ($lp_row_offset + 8))->setValue($lp['buyNowPrice']);
            $as->getCell('B' . ($lp_row_offset + 9))->setValue($lp['flexPricePerNight']);

//            $as->getCell('B' . ($lp_row_offset + 10))->setValue('Taxes');
//            $as->getCell('B' . ($lp_row_offset + 11))->setValue('Service Charges');
//            $as->getCell('B' . ($lp_row_offset + 12))->setValue('Fees');
            $i++;
        }

        // Booking conditions
        $as->getCell('A28')->setValue($package['termsAndConditions']);

        // Blackout Weekdays
        $as->getCell('A25')->setValue($this->viewVars['bo_weekdays']);
        **/

    }

    public function dump(
        $filename = 'spreadsheet'
    ) {
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Pragma: private');
        header('Cache-control: private, must-revalidate');
        header("Content-Disposition: inline; filename=$filename.xlsx");
        $writer = new PHPExcel_Writer_Excel2007($this->sheet);
        $tmpfname = tempnam("/tmp", "Ticket4769_");
        $writer->save($tmpfname);
        unset($writer);
        readfile($tmpfname);
        unlink($tmpfname);
    }

    public function addToCurrentOffset($n)
    {

        if (isset($n)) {
            $this->current_row_offset = $this->current_row_offset + $n;
        }
    }

    public function getCurrentRow()
    {
        return $this->current_row_offset;
    }

    public function getDefaultOffset()
    {
        return $this->defaultRowOffset;
    }
    public function beginValidityPeriod()
    {
        return true;
    }
    /*
     * Closes validity period.
     */
    public function endValidityPeriod()
    {

        return true;
    }
    public function setRatePerNightCell()
    {
        return true;
    }
    public function displayValidityPeriodBlock($validityPeriodIndex)
    {
        $this->endValidityPeriod();
        return true;
    }

    private function setLastLineData($index,$rowLastLine)
    {
       if(isset($index)) {
           $this->validityPeriodArray[$index]['lastRow'] = $rowLastLine;
       }
        return true;
    }

    private function cellColor($cells,$color)
    {
        $as = $this->sheet->getActiveSheet();
        $as->getStyle($cells)->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => $color)
            ));
    }
    private function getMaxValidityRow()
    {
        $maxRow =  max($this->validityPeriodArray);
        return $maxRow['lastRow'];
    }
    private function amax($array){
        if(is_array($array)){
            foreach($array as $key => $value){
                $array[$key] = $this->amax($value);
            }
            return max($array);
        }else{
            return $array;
        }
    }

    private function getValidityTotalRowByKey($key)
    {
        if(isset($this->validityPeriodArray[$key])){
            return $this->validityPeriodArray[$key]['lastRow'];
        }
        return null;
    }

    private function addMultiDayInclusionSingleDayPrice($inclusionValue)
    {
        $inclusionValue = floatval($inclusionValue);
        $this->multiDayInclusionSingleDayPrice = $this->multiDayInclusionSingleDayPrice + $inclusionValue;
        return true;
    }
}