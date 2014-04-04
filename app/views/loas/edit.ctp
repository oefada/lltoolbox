<?php
$loa = $this->data;
echo $this->element("loas_subheader", array("loa" => $loa, "client" => $client));

$this->searchController = 'Clients';
$this->set('clientId', $this->data['Client']['clientId']);

echo $layout->blockStart('header');
echo $html->link(
    '<span><b class="icon"></b>Delete LOA</span>',
    array(
        'action' => 'delete',
        $form->value('Loa.loaId')
    ),
    array('class' => 'button del'),
    sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Loa.loaId')),
    false
);
echo $layout->blockEnd();
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
<style type="text/css">
    div.pub-status div.checkbox input[type="checkbox"] {
        width: 20px;
    }

    /*** TinyMCE Simple Skin***/
    .defaultSimpleSkin{
        display: block;
        margin: -20px 0 0 170px;
        position: relative;
    }
    .checkspelling{
        margin: -20px 0 0 180px;
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
<script type='text/javascript' src='/js/javascriptspellcheck/include.js'></script>

<script type='text/javascript'>
    $Spelling.SpellCheckAsYouType('spellSpan');
    //$Spelling.PopUpStyle="modalbox";
</script>
<h2 class="title">
    <?php __('Edit Loa');
    echo $html2->c($loa['Loa']['loaId'], 'LOA Id:')?>
</h2>

<div class="loas form">

<script type="text/javascript">
    /***
     * Script added by martin to allow for client notes
     */
    jQuery(function ($) {
        $(window).ready(function () {
            load_notes(<?= $loa['Loa']['loaId']; ?>, 6);
        });
    });

</script>
<div id="noteModule" style="position: absolute; top: 194px; left: 940px;"></div>
<?php

echo $form->create('Loa');
echo $form->submit('Submit');
echo '<fieldset>';
$timestamp = time();
//echo "<a><div style='float:left;' qs=\"time={$timestamp}\" class=\"edit-link\" name=\"prepdocument/{$this->data['Loa']['loaId']}\" title=\"Prepare LOA Document - {$this->data['Loa']['loaId']}\">Prep LOA Document</div>";
//echo '</a>';
?>
<!--<div class="actions">
    <ul>

        <li>
            <?php
            if ($showDocument == true) {
                echo $html->link(
                    'Prep Document',
                    $this->webroot . 'loas/prepdocument/' . $loa['Loa']['loaId'] . '/' . $loa['Loa']['clientId'],
                    array(
                        'title' => 'Prepare LOA Document - Loa # ' . $this->data['Loa']['loaId'],
                        'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                        'complete' => 'closeModalbox()'
                    ),
                    null,
                    false
                );
            }
            ?></li>
    </ul>
</div>-->
<?

// for editing membershipBalance, totalKept, totalRemitted, totalRevenue
$uname = $userDetails['username'];
$userGroupsArr = $userDetails['groups'];
$userPermArr = array('dpen', 'kferson', 'emendoza', 'rfriedman', 'jlagraff', 'mtrinh', 'oefada', 'jwoods');

$isProposal  = ($this->data['Loa']['loaLevelId'] ==0)?true:false;

$disable_advanced_edit = (in_array($uname, $userPermArr) ||
    in_array('Production', $userGroupsArr) ||
    in_array('Accounting', $userGroupsArr)||
    $isProposal) ? false : true;

if ($isProposal ||
    (in_array($uname, $userPermArr) ||
     in_array('Production', $userDetails['groups']) ||
     in_array('Accounting', $userGroupsArr) ||
     in_array('am', $userDetails['groups']))) {
    $disabled = false;
} else {
    $disabled = true;
}

$feeAmountDisabled = $disabled;
if ($this->data['Loa']['loaLevelId'] == 2 &&
    (!in_array('Accounting', $userGroupsArr) ||(!in_array('Geeks', $userGroupsArr))) ) {
    $feeAmountDisabled = true;
}

// for editing membershipPackagesRemaining
$userPermArr = array('emendoza', 'kferson', 'jlagraff', 'mtrinh');
$disable_mp = (in_array($uname, $userPermArr) ||
    in_array('Production', $userDetails['groups']) ||
    in_array('Accounting', $userGroupsArr)
) ? false : true;

?>

<div style="clear:both;"></div>

<?php
echo '<div class="controlset4">' . $multisite->checkbox('Loa') . '</div>';
echo $form->input('accountTypeId', array('label' => 'Account Type'));
echo $form->input('loaLevelId', array('disabled' => $disabled, 'label' => 'LOA Level'));
echo $form->hidden('loaLevelId_prev', array('value'=>$form->value('loaLevelId')));
echo $form->input('renewalResult', array('type' => 'select', 'options' => $renewalResultOptions));
echo $form->input(
    'startDate',
    array(
        'minYear' => date('Y', strtotime('January 01, 2000')),
        'maxYear' => date('Y', strtotime('+5 year')),
        'timeFormat' => ''
    )
);
echo $form->input(
    'endDate',
    array(
        'minYear' => date('Y', strtotime('January 01, 2000')),
        'maxYear' => date('Y', strtotime('+5 year')),
        'timeFormat' => ''
    )
);
echo $form->input(
    'loaMembershipTypeId',
    array(
        'label' => 'Membership Type',
        'disabled' => $disable_advanced_edit
    )
);
echo $form->input('loaPaymentTermId', array(
        'type' => 'select',
        'label' => 'Payment Terms',
        'default' => 7,
        //'empty' => true,
    ));
echo $form->input('revenueSplitPercentage',array('label'=>'Rev Split % kept by LL'));
$enable_est = !$disable_advanced_edit && ($loa['Loa']['loaMembershipTypeId'] == 3) ? true : false;
//allow empty selection AND select current setting
echo $form->input('loaInstallmentTypeId', array(
        'type' => 'select',
        'label' => 'Installment Types',
        'empty' => true,
        //'selected'=>$this->data['Loa']['loaMembershipTypeId']
    ));

echo $form->input('membershipFee', array('disabled' => $feeAmountDisabled));
echo $form->input('membershipTotalPackages');
echo $form->input('membershipTotalNights');

echo $form->input('auctionCommissionPerc', array('label' => 'Auction % Commission'));
echo $form->input('buynowCommissionPerc', array('label' => 'BuyNow % Commission'));
echo $form->input('numEmailInclusions');
echo '<div><label>Client Segment</label><span>' . $client['Client']['segment'] . '</span></div>';
echo '<div class="spellSpan" id="spellSpan">';
echo $form->input(
    'notes',
    array(
        'label' => 'LOA Notes',
        'id' => 'loaNotes',
        'class' => 'loaNotes'
    )
);
echo '</div>';

?>
<a class="checkspelling" href="#" onclick="$Spelling.SpellCheckInWindow('spellSpan'); return false;">Check Spelling</a>

<?
echo $form->input('nonRenewalReason', array('type' => 'select', 'options' => $nonRenewalReasonOptions));
echo $form->input('nonRenewalNote', array('type' => 'textarea'));
echo $form->input('accountExecutive');
echo $form->input('accountManager');


if (isset($loa['Loa']['created'])) {
    echo '<div><label>Created</label><span>';
    echo $loa['Loa']['created'];
    echo '</span></div>';
}
if (isset($loa['Loa']['modified'])) {
    echo '<div><label>Modified</label><span>' . $loa['Loa']['modified'] . '</span></div>';
    echo '<div><label>Modified By</label><span>' . $loa['Loa']['modifiedBy'] . '</span></div>';
}
?>
<div class="collapsible">
    <div class="handle"><?php __('Accounting'); ?></div>
    <div class="collapsibleContent related">
                    <?php
                    echo $form->input('Loa.currencyId', array('label' => 'Item Currency'));
                    echo $form->input('payoffDate', array('empty' => true));
                    ?>
                     <div class="input text">
                     <label for="membershipFeeCopy">Membership Fee</label>
                         <span id="membershipFeeCopy">
                         </span>
                        </div>
                    <?
                    echo $form->input('membershipBalance', array('disabled' => $disable_advanced_edit));
                    ?>

                <div class="input text">
                    <label for="membershipTotalPackagesCopy">Membership Total Packages</label>
                                 <span id="membershipTotalPackagesCopy">
                                 </span>
                </div>
                <?
                echo $form->input('membershipPackagesRemaining', array('disabled' => $disable_mp));
                ?>
                <div class="input text">
                    <label for="membershipTotalNightsCopy">Membership Total Nights</label>
                             <span id="membershipTotalNightsCopy">
                             </span>
                </div>
        <?
                    echo $form->input('membershipNightsRemaining');
                    echo $form->input('membershipFeeEstimated', array('disabled' => $enable_est,'label' => 'Membership Estimated Fee'));
                    $enable_rvc = !$disable_advanced_edit && ($loa['Loa']['loaMembershipTypeId'] == 5) ? true : false;
                    echo $form->input('retailValueFee', array('disabled' => $enable_rvc));
                    echo $form->input('retailValueBalance', array('disabled' => $enable_rvc));
                    echo $form->input('totalRevenue', array('disabled' => $disable_advanced_edit, 'label' => 'Total Revenue'));
                    echo $form->input('totalRemitted', array('disabled' => $disable_advanced_edit));
                    if (in_array($uname, array('kferson', 'jlagraff', 'mtrinh', 'emendoza'))||
                        in_array('Accounting', $userGroupsArr) ||
                        in_array('Sales', $userGroupsArr)) {
                        echo $form->input('cashPaid');
                    } else {
                        echo $form->input('cashPaid', array('disabled' => true));
                    }
                    echo $form->input('totalKept', array('disabled' => $disable_advanced_edit));
                    echo $form->input('totalCommission', array('disabled' => $disable_advanced_edit));
                    echo $form->input('loaNumberPackages', array('label' => 'Commission-Free Packages'));

                    echo $form->input('luxuryLinkFee');
                    echo $form->input('familyGetawayFee');
                    echo $form->input('advertisingFee');

                    ?>

    </div>
    <!--#collapsibleContent-->
</div>
<!--#collapsible-->
<div class="collapsible">
    <div class="handle"><?php __('Marketing'); ?></div>
    <div class="collapsibleContent related">
        <div class="controlset">
            <?php
            echo $form->input('moneyBackGuarantee', array('label' => 'Money Back Guarantee'));
            echo $form->input('upgraded', array('label' => 'Risk Free Guarantee'));
            echo '</div>';

            echo '<div class="controlset" style="margin-left:-155px;margin-top:-20px;">';
            echo $form->input(
                'checkboxes',
                array(
                    'label' => false,
                    'type' => 'select',
                    'multiple' => 'checkbox',
                    'options' => $checkboxValuesArr,
                    'selected' => $checkboxValuesSelectedArr
                )
            );
            ?>
        </div>
        <?
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
<!--#collapsibleContent-->
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
                'onKeyDown' => 'limitText(emailNewsletterDates, 1000)',
                'onKeyUp' => 'limitText(emailNewsletterDates, 1000)'
            )
        );
        echo $form->input('averageDailyRate');
        echo $form->input(
            'customerApprovalDate',
            array(
                'empty' => true,
                'label' => 'Package in Date',
                'minYear' => date('Y', strtotime('January 01, 2000')),
                'maxYear' => date('Y', strtotime('+5 year')),
                'timeFormat' => ''
            )
        );

        echo $form->input(
            'packageLiveDate',
            array(
                'empty' => true,
                'label' => 'Package Live Date',
                'minYear' => date('Y', strtotime('January 01, 2000')),
                'maxYear' => date('Y', strtotime('+5 year')),
                'timeFormat' => ''
            )
        );
        ?>
    </div>
    <!--#collapsibleContent-->
</div>
<!--#collapsible-->
<?
    echo $form->input('loaId', array('type' => 'hidden'));
    echo $form->input('clientId', array('type' => 'hidden'));
    ?>
    </fieldset>
    <div class="buttonrow">
        <?php echo $form->end('Submit'); ?>
    </div>
</div>
<?php
//if ($showDocument == true) {
    ?>
<div class="collapsible">
    <div class="handle"><?php __('LOA Documents'); ?></div>
    <div class="collapsibleContent related">
        <div class="actions">
            <ul>
                <li>
                    <?php
                        echo $html->link(
                            'Prep Document',
                            $this->webroot . 'loas/prepdocument/' . $loa['Loa']['loaId'] . '/' . $loa['Loa']['clientId'],
                            array(
                                'title' => 'Prepare LOA Document - Loa # ' . $this->data['Loa']['loaId'],
                                'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                                'complete' => 'closeModalbox()',
                                'class='=>'button add'
                            ),
                            null,
                            false
                        );
                    ?>
                </li>
            </ul>

        </div>
    </div><!--#handle-->
</div><!--#collapsible-->
<?
//}
?>
<div class="collapsible">
    <div class="handle"><?php __('Related LOA Tracks'); ?></div>
    <div class="collapsibleContent related">
        <div class="actions">
            <ul>

                <li>
                    <?php
                    echo $html->link(
                        'Add new LOA track',
                        '/loas/' . $loa['Loa']['loaId'] . '/tracks/add/',
                        array(
                            'title' => 'Add LOA Track',
                            'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                            'complete' => 'closeModalbox()'
                        ),
                        null,
                        false
                    );
                    ?></li>
            </ul>
        </div>
        <?php if (!empty($loa['Track'])): ?>
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th><?php __('Track Name') ?></th>
                    <th><?php __('Revenue Model'); ?></th>
                    <th><?php __('Expiration Criteria'); ?></th>
                    <th class="actions"><?php __('Actions'); ?></th>
                </tr>
                <?php
                $i = 0;
                foreach ($loa['Track'] as $track):
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td><?php echo $track['trackName'] ?></td>
                        <td><?php echo $track['RevenueModel']['revenueModelName']; ?></td>
                        <td><?php echo $track['ExpirationCriterium']['expirationCriteriaName']; ?></td>
                        <td class="actions">
                            <?php
                            echo $html->link(
                                'Edit',
                                '/tracks/edit/' . $track['trackId'],
                                array(
                                    'title' => 'Edit LOA Track',
                                    'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                                    'complete' => 'closeModalbox()'
                                ),
                                null,
                                false
                            );
                            ?>
                            <?php echo $html->link(
                                __('Delete', true),
                                array('controller' => 'tracks', 'action' => 'delete', $track['trackId']),
                                null,
                                sprintf(__('Are you sure you want to delete # %s?', true), $track['trackId'])
                            ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    </div>
</div>
<div class="collapsible">
    <div class="handle"><?php __('Audit History'); ?></div>
    <div class="collapsibleContent related">
        <div class="actions">
            <ul>
                <li>
                    <?php
                    if (!empty($loaAudit)) {
                        ?>
                        <table class="summary">
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Changes</th>
                            <?
                            $i = 0;
                            foreach ($loaAudit as $key => $logDataVal) {
                                $class = null;

                                if ($i % 2 == 0) {

                                    $class = ' class="altrow"';
                                }?>
                                <tr <?php echo $class ?>>
                                    <td><?= date('M d, Y h:i a', strtotime($logDataVal['Log']['created'])) ?></td>
                                    <td><?= $logDataVal['Log']['samaccountname'] ?></td>
                                    <td><table cellspacing="2" style="font-size:80%;">
                                        <tr> <th>Changed</th><th>From</th><th>To</th> </tr>
                                            <? $changedFields = null;
                                            foreach (explode(',', $logDataVal['Log']['change']) as $change) {
                                            preg_match('/(\w+?) \((.*?)\) => \((.+?)\)/s', $change, $matches);
                                            list($search, $field, $from, $to) = $matches;
                                            ?>
                                        <tr style="border-bottom:1px dashed #CCC"> <td style="width:150px;"><?=$field?></td><td><?=$from?></td><td><?=$to?></td> </tr>
                                            <? }?>
                                    </table><?=
                                        $changedFields;
                                        unset($changedFields);
                                        ?></td>
                                </tr>
                            <?
                                //end foreach
                                $i++;
                            }?>
                        </table>
                        <? //end if
                    }?>
                </li>
            </ul>

        </div>
    </div><!--#handle-->
</div><!--#collapsible-->
<script type="text/javascript">

    jQuery(document).ready(function () {
        var $ = jQuery,
            loaPaymentTermsElement = $("#LoaLoaPaymentTermId"),
            loaRevenueSplitPercentageElement = $("#LoaRevenueSplitPercentage");

        if (loaPaymentTermsElement.find("option:selected").text() !== 'Revenue Split') {
            loaRevenueSplitPercentageElement.parent().hide();
        }

        loaPaymentTermsElement.on("change", function() {
            if ($(this).find("option:selected").text() === 'Revenue Split') {
                loaRevenueSplitPercentageElement.parent().show();
            } else {
                loaRevenueSplitPercentageElement.parent().hide();
            }
        })

        $("#LoaEditForm").submit(function () {
            <?if ($form->data['Loa']['membershipBalance'] > 0){?>
            if ($("#LoaMembershipBalance").length>0){
                if ($("#LoaMembershipBalance").val() == 0 && <?=$form->data['Loa']['membershipBalance']?> > 0) {
                    if (confirm('are you sure you want to set the membership balance to ZERO?') == false) {
                        return false;
                    }
                }
            }
            <? }?>
            if ($("#LoaSitesLuxuryLink").attr('checked') == false && $("#LoaSitesFamily").attr('checked') == false) {
                alert("You must check off which site(s) this is for.");
                return false;
            } else {
                return true;
            }
        });

        function copyField(sourceSelector, targetSelector) {
            //set defaults values for target
            $sourceValue = $(sourceSelector).val();
            $target = $(targetSelector);

            if ($target.is("input")) {
                $(targetSelector).val($sourceValue);
            } else {
                $(targetSelector).text($sourceValue);
            }
            //watch for updates, update target
            $(document).on("change, keyup", sourceSelector, function () {
                var $sourceValue = $(this).val();
                $target = $(targetSelector);
                if ($sourceValue.length == 0) {
                    $(targetSelector).val('');
                    return false;
                }
                if ($target.is("input")) {
                    $(targetSelector).val($sourceValue);
                } else {
                    $(targetSelector).text($sourceValue);
                }
                return;
            });
        }
        copyField("#LoaMembershipFee",'#membershipFeeCopy');
        copyField("#LoaMembershipTotalNights","#membershipTotalNightsCopy");
        copyField("#LoaMembershipTotalPackages","#membershipTotalPackagesCopy");

    //modal handler
        $('div.edit-link').click(function() {

            if ($(this).attr('href') == undefined) {
                var url = '/loas/'+$(this).attr('name');


                if ($(this).attr('qs') != undefined) {
                    url += '/?' + $(this).attr('qs')+'ts='+new Date().getTime();

                }
            } else {
                var url = $(this).attr('href')+'/?overlayForm=1';
            }

            $('iframe#dynamicForm').attr('src', url);
            $('div#formContainer').dialog({modal:true,
                autoOpen:false,
                height:800,
                width:1100,
                title:'Edit'
            });

            $('div#formContainer').dialog('open');
        });
    });

    Event.observe('LoaLoaMembershipTypeId', 'change', toggle_fields);
    Event.observe(window, 'load', toggle_fields);
    function toggle_fields() {

        if ($('LoaLoaMembershipTypeId').getValue() == 3) {
            // # packages
            //$('LoaMembershipTotalPackages').enable();
            $('LoaRetailValueFee').disable();
            $('LoaRetailValueBalance').disable();
            $('LoaMembershipFeeEstimated').enable();
        } else if ($('LoaLoaMembershipTypeId').getValue() == 5) {
            // retail value credit
            //$('LoaMembershipTotalPackages').disable();
            $('LoaRetailValueFee').enable();
            $('LoaRetailValueBalance').enable();
            $('LoaMembershipFeeEstimated').enable();
        } else {
            //$('LoaMembershipTotalPackages').disable();
            $('LoaRetailValueFee').disable();
            $('LoaRetailValueBalance').disable();
            $('LoaMembershipFeeEstimated').disable();
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
<!-- CONTAINER FOR OVERLAYS ====================================================================-->

<div id="formContainer" style="display:none;overflow:hidden">
    <iframe id="dynamicForm" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0"
            scrolling="auto"></iframe>
</div>
