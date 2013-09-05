 <?
$loa = $this->data;
//$this->searchController = 'Clients';
 echo $html->css('pepper-grinder/jquery-ui-1.7.2.custom');
?>
<style type="text/css">
 /*.required{*/
     /*background-color:#FF0000;*/
     /*color:#FFF;*/
 /*}*/
        /* workarounds */
    html .ui-autocomplete { width:1px; } /* without this, the menu expands to 100% in IE6 */
    .ui-menu {
        list-style:none;
        padding: 2px;
        margin: 0;
        display:block;
        float: left;
    }
    .ui-menu .ui-menu {
        margin-top: -3px;
    }
    .ui-menu .ui-menu-item {
        margin:0;
        padding: 0;
        zoom: 1;
        float: left;
        clear: left;
        width: 100%;
    }
    .ui-menu .ui-menu-item a {
        text-decoration:none;
        display:block;
        padding:.2em .4em;
        line-height:1.5;
        zoom:1;
    }
    .ui-menu .ui-menu-item a.ui-state-hover,
    .ui-menu .ui-menu-item a.ui-state-active {
        font-weight: normal;
        margin: -1px;
    }
    .ui-menu .ui-menu-item a.ui-state-focus {
        color:#007ED1;
        border:1px dashed #CCC;
        background-image: none;
        background-color: #FFFFFF;
        background: -moz-linear-gradient(top,  #d7d6d2,  #ffffff);
        background-image: linear-gradient(top,  #d7d6d2,  #ffffff);
        background-image: -o-linear-gradient(top,  #d7d6d2,  #ffffff);
        background-image: -moz-linear-gradient(top,  #d7d6d2,  #ffffff);
        background-image: -webkit-linear-gradient(top,  #d7d6d2,  #ffffff);
        background-image: -ms-linear-gradient(top,  #d7d6d2,  #ffffff);
    }
</style>
<fieldset>
<!--    <legend class="handle">Generate LOA Document</legend>-->

<?
if (isset($client['ClientContact'])){
    echo $ajax->form(
        'save_document',
        'post',
        array(
            'url' => $this->webroot . "/loaDocuments/save_document/",
            'update' => 'save_result',
            'loading' => 'window.loaDoc.save_doc_loading()',
            'model' => 'loaDocument',
            'complete' => 'window.loaDoc.save_doc_complete()'
        )
    );
    echo $form->input(
        'LoaDocument.docDate',
        array(
            'label' => 'Document Date',
            'minYear' => date('Y', strtotime('-1 year')),
            'maxYear' => date('Y', strtotime('+5 year')),
            'timeFormat' => ''
        )
    );
    //echo '<div class="ui-widget">';
    echo $form->input('LoaDocument.signerName',array('label'=>'Rep Full Name: ','value'=>$userDetails['name']));
    //echo '</div>';
    echo $form->input('LoaDocument.signerTitle',array('label'=>'Rep Title','value'=>ucwords($userDetails['description'])));
    echo $form->input('LoaDocument.contactName', array('type' => 'select', 'options' => $arrContactsDropDown));
    echo $form->hidden('LoaDocument.loaId',array('value'=>$loaId));
    echo $form->hidden('LoaDocument.clientId',array('value'=>$clientId));
    echo $form->submit('Generate Agreement');
}
?>
</fieldset>


 <div class="loading_save" style="display:none;font-size:10px;text-align: center;"><img src="/img/spinner.gif"><br />processing...</div>
<div id="save_result"></div>

 <script type="text/javascript">


</script>
<h3>Previous Versions of Current LOA</h3>
   <div class="previousVersions" style="overflow-y: scroll; height:180px;">
   </div>
<div class="reloadPrevious" style="font-size:80%;padding:0 0 5px 18px;display:block;background:url('<?=$this->webroot.'img/icons/reload-icon16x16.png';?>') top left no-repeat;" onclick="loaDoc.listById(<?=$loaId;?>)">Reload</div>
<br />
<script type="text/javascript">

    window.loaDoc = (function() {

        var publics = {};

        publics.listById = function(loaId){
            (function($) {
               //show spinner
                $(".previousVersions").empty();
                $(".previousVersions").html('<img src="/img/ajax-loader2.gif" class="spinner-docList">');
            $.ajax({
                type: "POST",
                //same domain, do as html, jspo p not needed
                url: "<?php echo $this->webroot; ?>loaDocuments/listall/"+loaId+"?"+Math.random()*100000000,
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
        };
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
        };
        publics.availableTags =<?= json_encode($listSalesPeople)?>

            publics.hidDiv = function (mydiv) {
                (function ($) {
                    $(mydiv).hide();
                })(jQuery);
            };
        publics.save_doc_loading = function () {
            (function ($) {
                //empty previous results
                $('#save_result').empty();
                //show ajax loader
                $('.loading_save').show();
            })(jQuery);
        };
        publics.save_doc_complete = function () {
            (function ($) {
                $('.loading_save').hide();
                //load previous docs
                window.loaDoc.listById(<?=$loaId;?>);
            })(jQuery);
        };
        // Return our public symbols
        return publics;
    })();

    (function($) {
        $(document).ready(function(){
            //var fakedata = ['test1','test2','test3','test4','ietsanders'];
            $( "#LoaDocumentSignerName").autocomplete({
                minLength: 0,
                //source: window.loaDoc.availableTags,
                source: function(request, response){
                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep(window.loaDoc.availableTags, function( value ) {
                        return matcher.test(value.label) || matcher.test(value.value);
                    }) );
                },
                /*focus: function( event, ui ) {
                    $( "#LoaDocumentSignerName" ).val( ui.item.label );
                    return false;
                },*/
                 select: function( event, ui ) {
                     $( "#LoaDocumentSignerName" ).val( ui.item.label );
                     return false;
                 }

            })
            .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li>" )
                    .append( "<a style='font-size:90%;'>" + item.label + "<br>(" + item.value + ")</a>" )
                    .appendTo( ul );
            };
        });
    })(jQuery);
    window.loaDoc.listById(<?=$loaId;?>);
</script>


