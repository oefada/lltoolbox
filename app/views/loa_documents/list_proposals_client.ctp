<?
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']);
echo $this->element("loas_subheader", array("client" => $client));
$this->searchController = 'Clients';

?>
<style type="text/css">
    table.documents_table  th{

    }
</style>
<div id="loa-index" class="loas index">
    <h2 class="title">Viewing Loa Proposal Documents for <?=$client['Client']['name']?></h2>
<?

if (isset($documents)) {
    $form->create('Loa');
    //hack to be able to use our ajax_paginator element
    $this->params['form'] = $client['Client']['clientId'];

    echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'loa-index', 'showCount' => true));
   // $paginator->options(array('form' =>array('id'=>$client['Client']['clientId'])));
    $tbl = '<table cellpadding="0" cellspacing="0" class="documents_table">';
    $tbl .= $html->tableHeaders(
    array('<span class="font-size:80%">'.$paginator->sort('doc #','loaDocumentId').'</span>',
        $paginator->sort('Signer','signerName'),'Contact',
        $paginator->sort('Document Date','docDate'),
        $paginator->sort('Origin','sugarLoaId'),
        $paginator->sort('Created','created'), 'Action'),
    array('class' => ''),
    array('style' => 'border-collapse: collapse;color: #fff;')
    );
    foreach ($documents as $key => $doc) {

    $fromSugar = false;
    $docIdentifier = $doc['LoaDocument']['loaId'];
    if ($doc['LoaDocument']['isProposal'] == 1){
        //if it's from sugar

        $docIdentifier = $doc['LoaDocument']['sugarLoaId'];
    }

    $downloadUrl = $this->webroot.$this->params['controller'].'/download/'.$docIdentifier.'/'.$doc['LoaDocument']['loaDocumentId'];

    $loaDocIdPadded =  str_pad((int) $doc['LoaDocument']['loaDocumentId'],4,"0",STR_PAD_LEFT);
    $tbl .= $html->tableCells(
    array(
    '<span class="font-size:80%">'.$loaDocIdPadded.'</span>'
    ,$doc['LoaDocument']['signerName']
    ,$doc['LoaDocument']['contactName']
    ,date('F d, Y', strtotime($doc['LoaDocument']['docDate']))
    ,$doc['LoaDocumentSource']['name']
    ,date('m-d-Y H:m:s', strtotime($doc['LoaDocument']['created']))

    ,'<a href="'.$downloadUrl.'" target="_blank" text="download version '.$loaDocIdPadded.'">'.$html->image('pdf_download_24x24.png',array('alt'=>'Download Document')).'</a>'
    //,'<table style="border:none;padding:0px;"><tr><td style="border:none;"><a href="'.$downloadUrl.'" target="_blank" text="download version '.$loaDocIdPadded.'">'.$html->image('pdf_download.png',array('Download Document')).'</a></td></tr></table>'
    ),
    array('class'=>"altrow"),
    array('style'=>'')

    );
    }

    $tbl .= "</table><br />\n";
    echo $tbl;
    echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'loa-index'));
}else {
?>
    No Documents for this client.
    <?
    }
?>

</div>