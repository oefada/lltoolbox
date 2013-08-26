<?
if ($mode == 'jsonp') {
    //$this->layout = 'ajax';
    $this->layout = false;
    Configure::write('debug', 0);
    $arrResponse = json_encode($arrResponse);
    header("Content-Type: text/javascript; charset=utf-8");
    echo $arrResponse;
} else {
    $this->layout = false;
    Configure::write('debug', 0);
    //grid?
    if ($arrResponse['response'] == 1) {

        $tbl = "<table cellpadding='2' cellspacing='1' class='product_table'>";
        $tbl .= $html->tableHeaders(
            array('Version', '<b>Signed By</b>','Contact', '<b>Document Date</b>', '<b>Created</b>', 'Action'),
            array('class' => ''),
            array('style' => 'background-color:#CCC;padding:5px;')
        );
        foreach ($arrResponse['message'] as $key => $doc) {
            $downloadUrl = $this->webroot.$this->params['controller'].'/download/'.$doc['LoaDocument']['loaId'].'/'.$doc['LoaDocument']['loaDocumentId'];

            $loaDocIdPadded =  str_pad((int) $doc['LoaDocument']['loaDocumentId'],4,"0",STR_PAD_LEFT);
            $tbl .= $html->tableCells(
                array(
                    $loaDocIdPadded
                    ,$doc['LoaDocument']['signerName']
                    ,$doc['LoaDocument']['contactName']
                    ,date('F d, Y', strtotime($doc['LoaDocument']['docDate']))
                    ,date('m-d-Y H:m:s', strtotime($doc['LoaDocument']['created']))
                    ,'<table style="border:none;"><tr><td style="border:none;"><a href="'.$downloadUrl.'" target="_blank" text="download version '.$loaDocIdPadded.'">'.$html->image('pdf_download.png').'</a></td></tr></table>'
                ),
                array('class'=>"odd"),
                array('style'=>"background-color:#EDEDED;")

            );
        }
        $tbl .= "</table><br />\n";
        echo $tbl;
    } else {
        foreach ($arrResponse['message'] as $key => $error) {

            echo print_r($error);
        }

    }
}
?>
