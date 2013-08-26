<?
$loa = $this->data;
//$this->searchController = 'Clients';

?>
<style type="text/css">
 /*.required{*/
     /*background-color:#FF0000;*/
     /*color:#FFF;*/
 /*}*/
</style>
<fieldset>
<!--    <legend class="handle">Generate LOA Document</legend>-->

<?
if (isset($client['ClientContact'])){
    echo $ajax->form('save_document','post',array('url' => $this->webroot."/loaDocuments/save_document/", 'update' => 'save_result', 'model' => 'loaDocument', 'complete' => 'window.loaDoc.listById('.$loa['Loa']['loaId'].')'));
    echo $form->input(
        'LoaDocument.docDate',
        array(
            'label' => 'Document Date',
            'minYear' => date('Y', strtotime('-1 year')),
            'maxYear' => date('Y', strtotime('+5 year')),
            'timeFormat' => ''
        )
    );
    echo $form->input('LoaDocument.signerName',array('label'=>'Rep Full Name: ','value'=>$loa['Client']['managerUsername']));
    echo $form->input('LoaDocument.signerTitle',array('label'=>'Rep Title'));
    echo $form->input('LoaDocument.contactName', array('type' => 'select', 'options' => $arrContactsDropDown));
    echo $form->hidden('LoaDocument.loaId',array('value'=>$loa['Loa']['loaId']));
    echo $form->hidden('LoaDocument.clientId',array('value'=>$loa['Loa']['clientId']));
    echo $form->submit('Generate Agreement');
}
?>
</fieldset>
<div class="save_result"></div>
<div id="save_result"></div>
<?

$loa_pdf_html =  $this->element("loa_pdf", array("loa" => $loa, "client" => $client));

?>
<h3>Previous Versions of Current LOA</h3>
   <div class="previousVersions">
   </div>
<div class="reloadPrevious" style="font-size:80%;padding:0 0 5px 18px;display:block;background:url('<?=$this->webroot.'img/icons/reload-icon16x16.png';?>') top left no-repeat;" onclick="loaDoc.listById(<?=$loa['Loa']['loaId'];?>)">Reload</div>
<br />
<script type="text/javascript">

    window.loaDoc = (function() {

        var publics = {};

        publics.listById = function(loaId){
            (function($) {
               //show spinner
                $(".previousVersions").empty();
                $(".previousVersions").html('<img src="/img/spinner.gif" class="spinner-docList">');
            $.ajax({
                type: "POST",
                //same domain, do as html, jspo p not needed
                url: "<?php echo $this->webroot; ?>loaDocuments/listall/"+loaId+"",
                //dataType: 'jsonp',
                success: function(data, textStatus) {
                    //add html response
                    $(".previousVersions").html(data);
                    $(".spinner-docList").hide();
                },
                error: function() {
                    $(".previousVersions").html('<p class="error">could not retrieve LOA documents</p>');
                }
            });
            })(jQuery);
        }
        publics.createLoa = function(){
            (function($) {
                var dataToSend = {
                    page: location.href,
                    data: <?= json_encode($loa)?>
                };
                //show spinner
                $(".createLoaResult").html('<img src="/img/spinner.gif">');
                $.ajax({
                    type: "POST",
                    //url: location.hostname + "clients/testurl",
                    url: "<?php echo $this->webroot; ?>loaDocuments/createLoa/json/",
                    data: JSON.stringify(dataToSend),
                    success: function(data, textStatus) {
                        $(".createLoaResult").empty();
                        $(".createLoaResult").html(data);
                        $(".createLoaResult").css('background-color', '#BDE5F8');
                        this.listById(<?=$loa['Loa']['loaId'];?>)
                    },
                    error: function() {
                        $(".createLoaResult").html('<p>Unable to Generate</p>');
                    }
                });
            })(jQuery);
        }
        // Return our public symbols
        return publics;
    })();

   // window.loaDoc.listById(<?=$loa['Loa']['loaId'];?>);
</script>


