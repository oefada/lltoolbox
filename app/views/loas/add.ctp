
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
    /*** TinyMCE Simple Skin***/
    .defaultSimpleSkin {
        display: block;
        margin: -20px 0 0 170px;
        position: relative;
    }
</style>
<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
     tinyMCE.init({
     mode : "specific_textareas",
     editor_selector : "loaNotes",
     theme : "simple",
     force_p_newlines : false,
     force_br_newlines : true,/** make new lines use br **/
     forced_root_block : ''
     // init_instance_callback : 'resizeEditorBox',
     //auto_resize : true
     });
</script>
<?php
$this->pageTitle = $clientName . $html2->c($this->data['Loa']['clientId'], 'Client Id:') . '<br />' . $html2->c(
        'manager: ' . $client['Client']['managerUsername']
    );
$this->set('clientId', $this->data['Loa']['clientId']);
?>
<h2 class="title">New LOA</h2>
<div class="loas form">
    <?php echo $form->create('Loa'); ?>
    <fieldset>
        <div class="controlset4">
            <?php echo $multisite->checkbox('Loa'); ?>
        </div>
        <?php
        echo $form->input('accountTypeId', array('label' => 'Account Type'));
        echo $form->input('loaLevelId', array('label' => 'LOA Level'));
        echo $form->input(
            'startDate',
            array(
                'minYear' => date('Y', strtotime('-1 year')),
                'maxYear' => date('Y', strtotime('+5 year')),
                'timeFormat' => ''
            )
        );
        echo $form->input(
            'endDate',
            array(
                'minYear' => date('Y', strtotime('-1 year')),
                'maxYear' => date('Y', strtotime('+5 year')),
                'timeFormat' => ''
            )
        );
        echo $form->input('loaMembershipTypeId', array('label' => 'Membership Type'));
        echo $form->input('loaPaymentTermId', array(
                'label' => 'Payment Terms',
                'empty' => true
            ));
        echo $form->input('revenueSplitPercentage');
        echo $form->input('loaInstallmentTypeIds', array(
                'label' => 'Installment Types',
                'empty' => true
            ));
        echo '<div id="_LoaMembershipFee" style="padding:0px;">' . $form->input(
                'membershipFee'
            ) . '</div>';

        echo '<div id="_LoaMembershipTotalPackages" style="padding:0px;">';
        echo $form->input('membershipTotalPackages');
        echo '</div>';
        echo $form->input('membershipTotalNights');

        echo $form->input('auctionCommissionPerc', array('label' => 'Auction % Commission'));
        echo $form->input('buynowCommissionPerc', array('label' => 'BuyNow % Commission'));
        echo $form->input('numEmailInclusions', array('label'=>'Number of Email Impressions'));

        echo '<div><label>Client Segment</label><span>' . $client['Client']['segment'] . '</span></div>';
        echo $form->input(
            'notes',
            array(
                'label' => 'LOA Notes',
                'id' => 'loaNotes',
                'class'=>'loaNotes',
                'onKeyDown' => 'limitText(loaNotes, 300)',
                'onKeyUp' => 'limitText(loaNotes, 300)'
            )
        );
        echo $form->input('accountExecutive');
        echo $form->input('accountManager');
        echo $form->input('modifiedBy', array('type' => 'hidden', 'value' => $userDetails['username']));
        echo $form->input('clientId', array('type' => 'hidden'));
        ?>

        <div class="collapsible">
            <div class="handle"><?php __('Accounting'); ?></div>
            <div class="collapsibleContent related">

                            <?php
                            echo $form->input('currencyId');
                            echo '<div id="_LoaRetailValueFee" style="padding:0px;display:none;">' . $form->input(
                                    'retailValueFee',
                                    array('label' => 'Retail Value Credit')
                                ) . '</div>';
                            echo $form->input('loaNumberPackages', array('label' => 'Commission-Free<br /> Packages'));
                            echo '<div id="_LoaMembershipFeeEstimated" style="padding:0px;">';
                            echo $form->input('membershipFeeEstimated', array('label' => 'Membership Estimated Fee'));
                            echo '</div>';

                            echo $form->input('luxuryLinkFee');
                            echo $form->input('familyGetawayFee');
                            echo $form->input('advertisingFee');
                            ?>
            </div>
            <!--#handle-->
        </div>
        <!--#collapsible-->

        <div class="collapsible">
            <div class="handle"><?php __('Marketing'); ?></div>
            <div class="collapsibleContent related">
                            <?php
                            echo '<div class="controlset">' . $form->input('moneyBackGuarantee') ;
                            echo  $form->input(
                                    'upgraded',
                                    array('label' => 'Risk Free Guarantee')
                                ) . "</div>";
                            echo '<div class="controlset" style="margin-left:-155px;margin-top:-20px;">';
                            echo $form->input(
                                'checkboxes',
                                array(
                                    'label' => false,
                                    'type' => 'select',
                                    'multiple' => 'checkbox',
                                    'options' => $checkboxValuesArr
                                )
                            );
                            echo '</div>';
                            echo $form->input(
                                'homepageDates',
                                array(
                                    'label' => 'Homepage Placements',
                                    'id' => 'homepageDates',
                                    'onKeyDown' => 'limitText(homepageDates, 300)',
                                    'onKeyUp' => 'limitText(homepageDates, 300)'
                                )
                            );
                            echo $form->input(
                                'additionalMarketing',
                                array(
                                    'id' => 'additionalMarketing',
                                    'onKeyDown' => 'limitText(additionalMarketing, 300)',
                                    'onKeyUp' => 'limitText(additionalMarketing, 300)'
                                )
                            );
                            echo $form->input('socialMediaNotes', array('label' => 'Social Media Notes'));
                            ?>
            </div>
            <!--#handle-->
        </div>
        <!--#collapsible-->

        <div class="collapsible">
            <div class="handle"><?php __('Packaging'); ?></div>
            <div class="collapsibleContent related">
                            <?php
                            echo $form->input(
                                'emailNewsletterDates',
                                array(
                                    'label' => 'Packaging Notes',
                                    'id' => 'emailNewsletterDates',
                                    'onKeyDown' => 'limitText(emailNewsletterDates, 300)',
                                    'onKeyUp' => 'limitText(emailNewsletterDates, 300)'
                                )
                            );
                            echo $form->input('averageDailyRate');
                            echo $form->input(
                                'customerApprovalDate',
                                array(
                                    'empty' => true,
                                    'label' => 'Package in Date',
                                    'timeFormat' => '',
                                    'minYear' => date('Y', strtotime('January 01, 2000')),
                                    'maxYear' => date('Y', strtotime('+5 year'))
                                )
                            );
                            ?>
            </div>
            <!--#handle-->
        </div>
        <!--#collapsible-->
    </fieldset>
    <?php echo $form->end('Submit'); ?>
</div>


<script type="text/javascript">

    jQuery(document).ready(function () {
        var $ = jQuery,
            loaPaymentTermsElement = $("#LoaLoaPaymentTermId"),
            loaRevenueSplitPercentageElement = $("#LoaRevenueSplitPercentage");
        $("#LoaAddForm").submit(function () {
            if ($("#LoaSitesLuxuryLink").attr('checked') == false && $("#LoaSitesFamily").attr('checked') == false) {
                alert("You must check off which site(s) this is for.");
                return false;
            } else {
                return true;
            }
        });

        if (loaPaymentTermsElement.find("option:selected").text() !== 'Revenue Split') {
            loaRevenueSplitPercentageElement.parent().hide();
        }
        loaPaymentTermsElement.on("change", function() {
            if ($(this).find("option:selected").text() === 'Revenue Split') {
                loaRevenueSplitPercentageElement.parent().show();
                loaRevenueSplitPercentageElement.val('50');
            } else {
                loaRevenueSplitPercentageElement.parent().hide();
                loaRevenueSplitPercentageElement.val('');
            }
        })
    });
    Event.observe('LoaLoaMembershipTypeId', 'change', toggle_fields);
    Event.observe(window, 'load', toggle_fields);
    function toggle_fields() {
        if ($('LoaLoaMembershipTypeId').getValue() == 3) {
            $('_LoaMembershipFee').hide();
            //$('_LoaMembershipTotalPackages').show();
            $('_LoaMembershipFeeEstimated').show();
            $('_LoaRetailValueFee').hide();
        } else if ($('LoaLoaMembershipTypeId').getValue() == 5) {
            $('_LoaMembershipFeeEstimated').show();
            $('_LoaRetailValueFee').show();
           // $('_LoaMembershipTotalPackages').hide();
            $('_LoaMembershipFee').hide();
        } else {
            $('_LoaMembershipFee').show();
            //$('_LoaMembershipTotalPackages').hide();
            $('_LoaMembershipFeeEstimated').hide();
            $('_LoaRetailValueFee').hide();
        }
    }
    (function($) {
        //Autocompletes for LoaAccountExecutive and LoaAccountManager
        var listSalesPeople = <?= json_encode($listSalesPeople)?>;
        $(document).ready(function(){
            //var fakedata = ['test1','test2','test3','test4','ietsanders'];
            $( "#LoaAccountExecutive").autocomplete({
                minLength: 0,
                //source: window.loaDoc.availableTags,
                source: function(request, response){
                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep(listSalesPeople, function( value ) {
                        return matcher.test(value.label) || matcher.test(value.value);
                    }) );
                },
                select: function( event, ui ) {
                    $(  "#LoaAccountExecutive" ).val( ui.item.value );
                    return false;
                }
            })
                .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li>" )
                    .append( "<a style='font-size:90%;'>" + item.label + "<br>(" + item.value + ")</a>" )
                    .appendTo( ul );
            };

            //LoaAccountManager autocomplete
            $( "#LoaAccountManager").autocomplete({
                minLength: 0,
                //source: window.loaDoc.availableTags,
                source: function(request, response){
                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( $.grep(listSalesPeople, function( value ) {
                        return matcher.test(value.label) || matcher.test(value.value);
                    }) );
                },
                /*focus: function( event, ui ) {
                 $( "#LoaDocumentSignerName" ).val( ui.item.label );
                 return false;
                 },*/
                select: function( event, ui ) {
                    $(  "#LoaAccountManager" ).val( ui.item.value );
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

</script>
