<?php
/**
 * User: oefada
 * Date: 8/8/13
 * Time: 7:27 PM
 * To change this template use File | Settings | File Templates.
 */

if (!empty($errors)) {
    foreach ($errors as $errorMessage) {
        echo $errorMessage . "\n";
        echo '-----------------------';
    }
    exit;
}
    include(APP . 'vendors/tcpdf/tcpdf.php');

    define('PDF_LOA_CLIENT_NAME', $client['Client']['companyName']);

//K_PATH_IMAGES.'logo_example.jpg';

    $clientNameNoSp = str_replace(' ', '_', PDF_LOA_CLIENT_NAME);
    $clientNameClean = $clientNameNoSp = str_replace("'", '', $clientNameNoSp);

    $loaFileName = 'LOA_' . $clientNameClean . '_' . date('Y-m-d',strtotime($document['LoaDocument']['docDate'])) . '_' . $document['LoaDocument']['loaDocumentId'];

    //$myhtml = $this->element("loa_pdf", array("loa" => $loa));
    $myhtml = $document['LoaDocument']['content'];

    class MYPDF extends TCPDF
    {
        public $firstPage = true;
        //Page header
        public function Header()
        {
            // Set font
            $this->SetFont('helvetica', 'I', 8);
            if ($this->firstPage) {
                $this->firstPage = false;
                //$this->Cell(0, 15, '<< LuxuryLink Logo >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
                //logo
                $image_file = APP_ABSOLUTE_PATH . 'webroot/img/documents/pdf_ll_loa_header_logo.png';
                //measurement unit is millimeters.
                $this->Image(
                    $image_file,
                    10,
                    10,
                    183.88,
                    30.95,
                    'PNG',
                    '',
                    'T',
                    false,
                    300,
                    'C',
                    false,
                    false,
                    0,
                    false,
                    false,
                    false
                );
            } else {
                // Page number
                $this->Cell(
                    0,
                    15,
                    PDF_LOA_CLIENT_NAME . '/LLTG program, page ' . $this->getAliasNumPage(
                    ) . '/' . $this->getAliasNbPages(),
                    0,
                    false,
                    'C',
                    0,
                    '',
                    0,
                    false,
                    'M',
                    'M'
                );
            }
        }
    }

// create new PDF document
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
    $pdf->SetCreator('Toolbox');
    $pdf->SetAuthor('Luxury Link Travel Group');
    $pdf->SetTitle('Letter of Agreement- '. $clientNameNoSp);
//$pdf->SetSubject('TCPDF Tutorial');
    $pdf->SetKeywords('LOA, Luxury Link');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);


// set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

// set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
// ---------------------------------------------------------

// set font
    $pdf->SetFont('helvetica', '', 10);
// add a page
    $pdf->AddPage();
// output the HTML content
    $pdf->writeHTML($myhtml, true, false, true, false, '');
// reset pointer to the last page
    $pdf->lastPage();
//Close and output PDF document
   $pdf->Output($loaFileName . '.pdf', 'D');
    //$pdf->Output($loaFileName . 'pdf', 'FD');