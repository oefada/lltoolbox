<?php echo $javascript->link($this->webroot.'js/jquery/maskedinput/jquery.maskedinput.js', false); ?>

<style>
    div.ageRanges {
        position:relative;
        height:75px;
    }

    ul.optionList {
        position:absolute;
        left:150px;
    }

    ul.optionList li {
        list-style-type:none;
    }

    ul.optionList li input {
        width:20px;
    }

    div.checkbox input[type='checkbox'] {
        width:20px;
    }
    /***TinyMCE Start **/
    /*.mceToolbarTop * {*/
            /*float:left;*/
        /*}*/
    /*.mceToolbarTop select {*/
        /*width:auto!important;*/
    /*}*/
    /*.mceToolbarTop option {*/
        /*float:none;*/
    /*}*/
    /***TinyMCE End **/

</style>

<script type="text/javascript">
    var num = 1000;
    var clientId = <?php echo $client['Client']['clientId']; ?>;
    function addAmenity() {
        if($F('AmenitySelectId') > 0 && $('amenity_'+$F('AmenitySelectId')) == null) {
            $('amenitylist').down('ul').insert({'bottom':
                "<li id='amenity_"+$F('AmenitySelectId')+"' style='padding: 3px 0 3px 0'><span class=\"radio altcol\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='4'/></span><span class=\"radio\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='7' /></span><span class=\"radio altcol\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='3' /></span><span class=\"radio\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='2' /></span><span class=\"radio altcol\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='1'/></span><span class=\"radio\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='5' checked='checked'/></span><input type='hidden' name='data[ClientAmenityRel]["+num+"][amenityId]' value='"+$F('AmenitySelectId')+"' /><input type='hidden' name='data[ClientAmenityRel]["+num+"][clientId]' value='"+clientId+"' />"+$F('AmenitySelect')+'<a href="javascript: return false;" onclick="$(\'amenity_'+$F('AmenitySelectId')+'\').remove();">(remove)</a>'+"</li>"});
            num++;
            new Effect.Highlight($($F('AmenitySelectId')));
        }
    }

    function removeAmenity(amenityElem, hiddenName) {
        $(amenityElem).insert({top: '<input type="hidden" name="'+hiddenName+'[remove]" value="1" />'});
        $(amenityElem).hide();
    }
</script>
<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
    tinyMCE.init({
        //mode : "specific_textareas",
        // editor_selector : "ClientSiteExtended<?=$clientId;?>LongDesc",
        mode : "exact",
        elements : "ClientSiteExtended<?=$client['ClientSiteExtended'][0]['clientSiteExtendedId'];?>LongDesc\
        ,ClientInterview0Article",
        theme : "advanced",
        theme_advanced_buttons1 : "mybutton,bold,italic,underline,blockquote,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,code,iframe,cleanup,paste",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        plugins: "paste",
        force_p_newlines : false,
        force_br_newlines : true,/** make new lines use br **/
        forced_root_block : '',

        setup : function(ed) {
            tinyMCEfocusGrow(ed);
        }
        // init_instance_callback : 'resizeEditorBox',
        //auto_resize : true
    });
    tinyMCE.init({
        //mode : "specific_textareas",
        // editor_selector : "ClientSiteExtended<?=$clientId;?>LongDesc",
        mode : "exact",
        elements : "ClientSiteExtended<?=$client['ClientSiteExtended'][0]['clientSiteExtendedId'];?>Blurb",
        theme : "advanced",
        theme_advanced_buttons1 : "mybutton,bold,italic,underline,blockquote,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,undo,redo,link,unlink,code,iframe,cleanup,paste",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        plugins: "paste",
        force_p_newlines : false,
        force_br_newlines : true,/** make new lines use br **/
        forced_root_block : '',
        setup : function(ed) {
            tinyMCEfocusGrow(ed);
        }
        // init_instance_callback : 'resizeEditorBox',
        //auto_resize : true
    });

    function tinyMCEfocusGrow(ed){
        ed.onInit.add(function(ed) {
            //paste as plaintext by default
            ed.pasteAsPlainText = true;
            var dom = ed.dom,
                doc = ed.getDoc(),
                el = doc.content_editable ? ed.getBody() : (tinymce.isGecko ? doc : ed.getWin());
            tinymce.dom.Event.add(el, 'focus', function(e) {
//                    console.log('focus');
                $currentSelector = '#'+ed.id+'_ifr';

                $initialEditorHeight = jQuery($currentSelector).height();
                //expand size of tinyMCE
                jQuery($currentSelector).css('height','150px');

            })
            tinymce.dom.Event.add(el, 'blur', function(e) {
//                    console.log('blur');
//                    console.log(ed.id);
                jQuery('#'+ed.id+'_ifr').css('height',$initialEditorHeight+'px');
            })

        });
    }

</script>
<script type="text/javascript">


    jQuery(function($){
    //resize field
        $(document).ready(function() {
            alert('ready for focus');
            jQuery('#ClientSiteExtended<?=$client['ClientSiteExtended'][0]['clientSiteExtendedId'];?>LongDesc_ifr').css('height','250px');
            $("#ClientSiteExtended<?=$client['ClientSiteExtended'][0]['clientSiteExtendedId'];?>LongDesc_ifr" +
              ",#ClientSiteExtended<?=$client['ClientSiteExtended'][0]['clientSiteExtendedId'];?>Blurb_ifr" +
                ",#ClientInterview0Article_ifr").live('focus click', function() {
                    alert('focused!');
                });
        });
    });
</script>
<?php
$this->pageTitle = $this->data['Client']['name'].$html2->c($this->data['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$this->data['Client']['managerUsername']);

$is_luxurylink = false;
$is_family = false;
if (empty($this->data['Client']['sites'])) {
    $is_luxurylink = true;
}
foreach ($this->data['Client']['sites'] as $site) {
    switch($site) {
        case 'luxurylink':
            $is_luxurylink = true;
            break;
        case 'family':
            $is_family = true;
            break;
        default:
            $is_luxurylink = true;
    }
}
?>
<div class="clients form">
<h2 class="title">Client Details</h2>
<div style="float: right">
    <?php
    echo $html->link('<span><b class="icon"></b>Add Child Client</span>',
        "/clients/add/$clientId",
        array(
            'title' => 'Add Child Client',
            'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
            'complete' => 'closeModalbox()',
            'class' => 'button add'
        ),
        null,
        false
    );
    ?>

    <?php
    if (in_array('luxurylink', $this->data['Client']['sites'])) {
        echo $html->link('<span>Preview on LuxuryLink</span>', "http://www.luxurylink.com/luxury-hotels/preview.html?clid={$this->data['Client']['clientId']}&preview=client", array('target' => '_blank', 'class' => 'button'), null, false);
        # View the Client PDP
        echo $html->link('<span>View PDP on LuxuryLink</span>', "http://www.luxurylink.com/5star/".!empty($this->data['Client']['clientTypeSeoName'])?$this->data['Client']['clientTypeSeoName']:''."/{$this->data['Client']['seoLocation']}/{$this->data['Client']['seoName']}", array('target' => '_blank', 'class' => 'button'), null, false);
    }
    ?>
</div>


<script type="text/javascript">
    /***
     * Script added by martin to allow for client notes
     */
    jQuery(function($){
        $(window).ready(function(){
            load_notes(<?= $client['Client']['clientId']; ?>, 1);
        });
    });

</script>
<div id="noteModule" style="position: absolute; top: 280px; left: 850px;"></div>


<?php echo $form->create('Client');?>
<?php foreach($this->data['ClientSiteExtended'] as $site) {
    echo $form->hidden('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.clientSiteExtendedId', array('value' => $site['clientSiteExtendedId']));
}
?>
<fieldset>
<? echo $form->input('clientTypeId', array('label' => 'Client Type', 'empty' => true)); ?>


<div class="input select" style="width: 495px;">

    <div style="border: 1px solid #ccc; padding: 10px; width: 300px; height: 200px; overflow: auto; float: right;">
        <?
        foreach($collections as $key => $collect){
            if( isset($collectionsSelected[$key]) && $collectionsSelected[$key] == $key){ $checked = " checked"; } else { $checked = ""; }
            echo "<input type=\"checkbox\" id=\"data[Client][clientCollections][$key]\" name=\"data[Client][clientCollections][$key]\" value=\"$key\" $checked /> $collect <br />";
        }
        ?>
    </div>

    <label for="ClientClientCollections">Collection </span></label>
</div>

<div class="input text"><label>LOA Level</label><?=$this->data['ClientLevel']['clientLevelName']?></div>
<div class="controlset4">
    <label>Hide on</label>
    <?php foreach($this->data['ClientSiteExtended'] as $site): ?>
        <?php echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.inactive', array('label' => $multisite->displayName($site['siteId']), 'value' => $site['inactive'], 'checked' => ($site['inactive']) ? true : false)); ?>
    <?php endforeach;?>
</div>
<?	echo $form->hidden('sites', array('value' => implode(',', $this->data['Client']['sites'])));
foreach($this->data['ClientSiteExtended'] as $site) {
    echo $form->hidden('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.siteId', array('value' => $site['siteId']));
}
?>
<?php
echo $form->input('clientId');
echo $form->input('name', array('type' => 'hidden'));
echo $form->input('parentClientId', array('readonly' => 'readonly'));

if ($this->data['Client']['parentClientId']):
    echo $html->link('View Parent', '/clients/'.$this->data['Client']['parentClientId']);
endif;

echo $form->input('oldProductId', array('disabled' => 'disabled'));

echo $form->input('name', array('disabled' => !($this->data['Client']['createdInToolbox'] || $this->data['Client']['parentClientId'])));

if ($showAccountingId) {
    echo $form->input('accountingId');
}

?>
<?php

echo $form->input('url');
echo $form->input('checkRateUrl');
echo $form->input('numRooms');
echo $form->input('numRoomsText');
echo $form->input('starRating', array('type' => 'select', 'options' => array('3' => '3', '3.5' => '3.5', '4' => '4', '4.5' => '4.5', '5' => '5'), 'empty' => true));
echo $form->input('segment');

?>
<div class="input text"><label>PDP URL</label><?="http://www.luxurylink.com/5star/{$this->data['Client']['clientTypeSeoName']}/{$this->data['Client']['seoLocation']}/{$this->data['Client']['seoName']}"?></div>
<?php foreach($this->data['ClientSiteExtended'] as $site): ?>
    <div style="float: left; <?php echo (count($this->data['ClientSiteExtended']) == 2) ? 'clear:right;width:47%;' : 'width:100%;'?>" class="multiSiteNarrow multiSiteSingle">
        <?php
        $longDescExtraTitle = ($site['siteId'] == 2) ? '(About)' : '';
        echo "<span class='siteName'>{$multisite->displayName($site['siteId'])} - Long Desc $longDescExtraTitle</span>";
        echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.longDesc', array('label'=>false, 'value' => $site['longDesc']));
        echo "<span class='siteName'>{$multisite->displayName($site['siteId'])} - Blurb</span>";
        echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.blurb', array('label'=>false, 'value' => $site['blurb']));
        echo "<span class='siteName'>{$multisite->displayName($site['siteId'])} - Keywords</span>";
        echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.keywords', array('label'=>false, 'value' => $site['keywords']));
        ?>
    </div>
<?php endforeach; ?>
<div class="controlset">
    <?echo $form->hidden('showTripAdvisorReview');?>
    <?echo $form->input('hideUserReviews');?>
</div>

<fieldset class="collapsible">
    <legend class="handle">Pegasus</legend>
    <div class="collapsibleContent">
        <?echo $form->input('isPegasusEnabled', array('label' => 'Enabled'));?>
        <?echo $form->input('pegasusBrandId', array('label' => 'Brand', 'empty' => true)); ?>
        <?echo $form->input('pegasusPropertyCode', array('label' => 'Property Code'));?>
        <?echo $form->input('pegasusRackCode', array('label' => 'Rack Rate Code'));?>
        <?echo $form->input('pegasusGuaranteeMethod', array('label' => 'Guarantee Method', 'options'=>array('AG'=>'Agency Guarantee', 'AD'=>'Agency Deposit', 'CG'=>'CC Guarantee', 'CD'=>'CC Deposit'), 'empty' => true)); ?>
        <?echo $form->input('isNewPdpEnabled', array('label' => 'Use New PDP'));?>
    </div>
</fieldset>

<fieldset class="collapsible">
    <legend class="handle">Contact Details</legend>
    <div class="collapsibleContent">
        <?php
        echo $form->input('phone1');
        echo $form->input('phone2');
        echo $form->input('fax', array('type' => 'hidden'));
        echo $form->input('estaraPhoneLocal', array('label'=>'Toll-Free Tracking #','title'=>'Please update Toll-Free Tracking # in the following format: N-NNN-NNN-NNNN'));
        echo $form->input('estaraPhoneIntl', array('label'=>'Intl / Direct Phone #'));
        echo $form->input('contactLL', array('type' => 'checkbox',
                'label' => 'Use LL/FG contact info instead of client\'s on PDP',
                'class' => 'contactLL-align'));
        ?>
        <script type="text/javascript">



            (function($) {
                $(function() {
                    //jQuery("ClientEstaraPhoneLocal").mask('99-99');
                    $("#ClientEstaraPhoneLocal").mask("1-999-999-9999");

                });
            })(jQuery);

            (function($) {


                $(document).ready(function() {

                    $('input' ).tooltip({

                        open: function (event, ui) {
                            ui.tooltip.css("max-width", "250px");
                            ui.tooltip.css("color", "red");
                            ui.tooltip.css("font-size", "10px");
                            ui.tooltip.css("padding", "5px");
                            ui.tooltip.css("margin-left", "850px");
                            ui.tooltip.css("z-index", "1000");
                            ui.tooltip.css("-webkit-box-shadow", "0px 0px 5px 1px #999");
                            ui.tooltip.css("box-shadow", "0px 0px 5px 1px #999");

                        },
                        position: {
//                                my: "center center",
//                                at: "right right"
                            my: "left top",
                            at: "left top"
                        }
                    });
                });

            })(jQuery);

        </script>


        <? if(isset($client['Address'])): ?>
            <h4>Addresses</h4>
            <?php foreach ($client['Address'] as $address):
                if($address['address1'] or $address['address2'] or $address['city'] or $address['stateName'] or $address['postalCode']):
                    ?>

                    <div style="position: relative; float: left; width: 220px; height: 120px; clear: none; border: 1px solid #e5e5e5; margin-bottom: 5px; background: url(/img/bgshade-brown.gif) repeat-x;">
                        <?php if ($address['address1']):
                            echo $address['address1']."<br />";
                        endif ?>
                        <?php if ($address['address2']):
                            echo $address['address2']."<br />";
                        endif ?>
                        <?php if ($address['postalCode']):
                            echo $address['postalCode']."<br />";
                        endif ?>
                        <div style="position: absolute; bottom: 0;"><?=$html->link('Edit', array('controller' => 'addresses', 'action' => 'edit', $address['addressId'])) ?> | <?php echo $html->link(__('Delete', true), array('controller' => 'addresses', 'action'=>'delete', $address['addressId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $address['addressId'])); ?></div>
                    </div>
                <?php
                endif;
            endforeach;
        endif;?>
        <div style="padding: 5px; margin-top: 10px">
            <h2>Contacts</h2>
            <?php foreach ($this->data['ClientContact'] as $c): ?>
                <div class="clientContact clientContactType<?=$c['clientContactTypeId']?>">
                    <strong>Name:</strong> <?=$c['name']?><br />
                    <strong>Title:</strong> <?=$c['businessTitle']?><br />
                    <strong>Email:</strong> <?=$c['emailAddress']?><br />
                    <strong>Phone:</strong> <?=$c['phone']?><br />
                    <strong>Fax:</strong> <?=$c['fax']?>
                </div>
            <?php endforeach; ?>
            <div style="clear: both; font-size: 10px; color: #333">
                <?=$html->image('page_white_star.png')?> Reservation main contact<br />
                <?=$html->image('house.png')?> Home page notification contact<br />
                <?=$html->image('edit.png')?> Reservation Copy
                <?php if (empty($this->data['ClientContact'])) echo 'No Client Contacts available.'?>
            </div>
        </div>
</fieldset>
<fieldset class="collapsible">
    <legend class="handle">Geographic Details</legend>
    <div class="collapsibleContent">
        <?php
        echo $form->input('customMapLat');
        echo $form->input('customMapLong');
        echo $form->input('customMapZoomMap', array('label' => 'Custom Map Zoom Level'));
        ?>
        <br /><br />
        <?
        echo $form->input('address1');
        echo $form->input('address2');
        echo $form->input('postalCode');
        ?>

        <div class="input text">
            <label for="countryDisplay">Country</label>
            <span id="countryDisplay"><?= $countryIds[$this->data['Client']['countryId']]; ?></span>
        </div>
        <div class="input text">
            <label for="stateDisplay">State</label>
            <span id="stateDisplay"><?= $stateIds[$this->data['Client']['stateId']]; ?></span>
        </div>
        <div class="input text">
            <label for="cityDisplay">City</label>
            <span id="cityDisplay" style="text-decoration:underline; color:#336699; cursor:pointer;"><?= (isset($cityIds[$this->data['Client']['cityId']])) ? $cityIds[$this->data['Client']['cityId']] : 'None'; ?></span>
        </div>
        <div id='clientLocator' style="margin:10px 0; border: 1px solid #e2e2e2; background-color: #f0f0f0; display: none;">
            <?
            echo $form->input('countryId', array('type'=>'hidden'));
            echo $form->input('stateId', array('type'=>'hidden'));
            echo $form->input('cityId', array('type'=>'hidden'));
            echo $form->input('cityIdUpdated', array('type'=>'hidden'));
            echo $form->input('locatorCountry', array('type'=>'select', 'label'=>'Select Country', 'empty'=>'--', 'options'=>$countryIds, 'default'=>$this->data['Client']['countryId']));
            echo $form->input('locatorState', array('type'=>'select', 'label'=>'Select State', 'empty'=>'--', 'options'=>$stateIds, 'default'=>$this->data['Client']['stateId']));
            echo $form->input('locatorCity', array('type'=>'select', 'label'=>'Select City', 'empty'=>'--', 'options'=>$cityIds, 'default'=>$this->data['Client']['cityId']));
            ?>
        </div>
        <?php
        echo $javascript->link('jquery/jquery',true);
        echo $javascript->link('jquery/jquery-noconflict',true);
        ?>
        <?php echo $html->css('//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery.ui.all.css', null, array(), false); ?>

        <?php //echo $javascript->link('/js/jqueryui/1.10.2/jquery-ui.min.js', true); ?>

        <?php echo $javascript->link('//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', true); ?>

        <script>


            jQuery(function($) {

                $("#cityDisplay").click(function() {
                    $('#clientLocator').toggle('slow');
                });

                $("#ClientLocatorCountry").change(function(){
                    $.getJSON("/countries/get_states_locator",{id: $(this).val()}, function(data) {
                        $('#ClientLocatorState')[0].options.length = 0;
                        $('#ClientLocatorCity')[0].options.length = 0;
                        $('#ClientLocatorState')[0].options.add(new Option('--', ''));
                        $('#ClientLocatorCity')[0].options.add(new Option('--', ''));
                        for (state in data.states) {
                            if (data.states.hasOwnProperty(state)) {
                                $('#ClientLocatorState')[0].options.add(new Option(data.states[state], state));
                            }
                        }
                    })
                });

                $("#ClientLocatorState").change(function(){
                    $.getJSON("/states/get_cities_locator",{id: $(this).val()}, function(data) {
                        $('#ClientLocatorCity')[0].options.length = 0;
                        $('#ClientLocatorCity')[0].options.add(new Option('--', ''));
                        for (city in data.cities) {
                            if (data.cities.hasOwnProperty(city)) {
                                $('#ClientLocatorCity')[0].options.add(new Option(data.cities[city], city));
                            }
                        }
                    })
                });

                $("#ClientLocatorCity").change(function(){
                    var city = $('#ClientLocatorCity').val();
                    $('#ClientCityId').val(city);
                    $('#ClientCityIdUpdated').val(city);

                    $.getJSON("/cities/ajaxinfo",{id: city}, function(data) {
                        $('#countryDisplay').html(data.info.countryName);
                        $('#stateDisplay').html(data.info.stateName);
                        $('#cityDisplay').html(data.info.cityName);
                        $('#ClientCountryId').val(data.info.cid);
                        $('#ClientStateId').val(data.info.sid);
                    });

                    if (city != '') {
                        $('#clientLocator').slideUp('slow');
                    }
                });

                $("#CopyLoc").click(function() {
                    var country = $("#countryDisplay").html();
                    var state   = $("#stateDisplay").html();
                    var city    = $("#cityDisplay").html();

                    var copyLoc = city;
                    if (country != "United States" && country != "Canada") {
                        copyLoc = copyLoc + ", " + country;
                    } else {
                        copyLoc = copyLoc + ", " + state;
                    }

                    $("#ClientLocationDisplay").val(copyLoc);
                    return false;
                });
            });
        </script>
        <?

        echo $form->input('locationDisplay',array('after' => '<button id="CopyLoc">Copy Location</button>'));
        echo $form->hidden('locationDisplay_prev',array('value'=>$form->value('locationDisplay')));
        echo $form->hidden('clientTypeId_prev',array('value'=>$form->value('clientTypeId')));

        ?><?php
        echo $form->input('airportCode');
        echo $form->input('timeZone', array('options'=> array(''=>'--',
                'UTC-12'=>'UTC-12','UTC-11'=>'UTC-11',
                'UTC-10'=>'UTC-10','UTC-9'=>'UTC-9',
                'UTC-8'=>'UTC-8','UTC-7'=>'UTC-7',
                'UTC-6'=>'UTC-6','UTC-5'=>'UTC-5',
                'UTC-4'=>'UTC-4','UTC-3'=>'UTC-3',
                'UTC-2'=>'UTC-2','UTC-1'=>'UTC-1',
                'UTC 0'=>'UTC 0','UTC+1'=>'UTC+1',
                'UTC+2'=>'UTC+2','UTC+3'=>'UTC+3',
                'UTC+4'=>'UTC+4','UTC+5'=>'UTC+5',
                'UTC+6'=>'UTC+6','UTC+7'=>'UTC+7',
                'UTC+8'=>'UTC+8','UTC+9'=>'UTC+9',
                'UTC+10'=>'UTC+10','UTC+11'=>'UTC+11',
                'UTC+12'=>'UTC+12')));
        ?>
    </div>


</fieldset>



<?php // CLIENT AMENITIES ========================================================================= ?>

<script type="text/javascript">
    function refreshCurrentAmenities(amenityTypeId) {
        var amenities = new Array();
        $$('#amenityType' + amenityTypeId + ' input').find(function(e) {
            if (e.checked) {
                amenities.push($('amenity-label-' + $(e).getValue()).innerHTML);
            }
        });
        amenities = amenities.join(', ');
        $('currentAmenities' + amenityTypeId).update(amenities);
    }
</script>

<fieldset class="collapsible">
    <legend class="handle">Amenities <?=$html2->c($client['ClientAmenityRel']); ?></legend>
    <div class="collapsibleContent">

        <script>
            jQuery(function() {
                var $ = jQuery;
                $("#amenities-accordion").accordion({
                    collapsible: true,
                    active: false,
                    autoHeight: false
                });
            });
        </script>
        <div id="amenities-accordion">
            <?php foreach ($client['ClientAmenityTypeRel'] as $amenityType): ?>
                <?php if (isset($amenityType['amenities'])): ?>
                    <h3><a href="#"><?php echo $amenityType['amenityTypeName']?></a></h3>
                    <div>
                        <?php if ($amenityType['clientAmenityTypeRelId']): ?>
                            <input type='hidden' name='data[ClientAmenityTypeRelId][<?php echo $amenityType['amenityTypeId']; ?>]' value='<?php echo $amenityType['clientAmenityTypeRelId']; ?>'/>
                        <?php endif; ?>
                        <strong>Description:</strong><br/>
                        <textarea name='data[ClientAmenityTypeRel][<?php echo $amenityType['amenityTypeId']; ?>]' style='width:308px; border:1px solid silver; font-size:50px;'><?php echo $amenityType['description']; ?></textarea>

                        <table border="0">
                            <tr valign="bottom" bgcolor="#bbb">
                                <td width="400"></td>
                                <?php foreach($client['ClientAmenityTypeRel'] as $amenityType2): ?>
                                    <td align="center" style="vertical-align: bottom !important;">
                                        <?php echo $amenityType2['amenityTypeName']?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php $amenities = $amenityType['amenities']; ?>
                            <?php foreach($amenities as $key => $amenity): ?>
                                <?php $bgcolor = ($key % 2) ? '#eee' : '#fff'; ?>
                                <tr bgcolor="<?php echo $bgcolor; ?>">
                                    <td><?=$amenity['amenityName']?></td>
                                    <?php foreach ($client['ClientAmenityTypeRel'] as $amenityType3): ?>
                                        <?php $checked = ($amenity['checked'] && $amenity['amenityTypeId'] === $amenityType3['amenityTypeId']) ? ' checked' : ''; ?>
                                        <?php $disabled = ($amenity['amenityTypeId'] !== $amenityType3['amenityTypeId']) ? ' disabled="disabled"' : ''; ?>
                                        <td align="center">
                                            <?php if($disabled == ''){ ?>
                                            <input
                                                type="checkbox"
                                                name="data[ClientAmenityRel][<?php echo $amenity['amenityId'] ?>]"
                                                value="<?php echo $amenity['amenityId'] ?>"
                                                <?php echo $disabled ?>
                                                <?php echo $checked; ?>
                                                />
                                            <? } ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="clear"><a href="/amenities">Manage Amenities</a></div>
    </div>
</fieldset>

<?php // END CLIENT AMENITIES ===================================================================== ?>



<fieldset class="collapsible">
    <legend class="handle">Themes (<?php echo $themesCount; ?>)</legend>
    <div class="collapsibleContent">
        <?php if ($is_luxurylink): ?>
            <span class="siteName"><strong>Luxury Link</strong></span>
        <?php endif; ?>
        <?php // if ($is_family): ?>
            <span class="siteName"><strong>Family Getaway</strong></span>
        <?php // endif; ?>
        <br />
        <?php foreach($themes as $theme): ?>
            <?php $checkedSite1 = '';
            $checkedSite2 = '';
            ?>
            <?php if (!empty($theme['ClientThemeRel'])): ?>
                <input type="hidden" name="data[Theme][<?php echo $theme['Theme']['themeId'] ?>][clientThemeRelId]" value="<?php echo $theme['ClientThemeRel'][0]['clientThemeRelId'] ?>" />
                <?php foreach ($theme['ClientThemeRel'][0]['sites'] as $site): ?>
                    <?php   switch ($site) {
                        case 'luxurylink':
                            $checkedSite1 = ' checked';
                            break;
                        case 'family':
                            $checkedSite2 = ' checked';
                            break;
                        default:
                            break;
                    }
                    ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($is_luxurylink): ?>
                <input class="themeCheckbox" type="checkbox" name="data[Theme][<?php echo $theme['Theme']['themeId'] ?>][sites][]" value="luxurylink" <?php echo $checkedSite1; ?> />
            <?php endif; ?>
            <?php //if ($is_family): ?>
                <input class="themeCheckbox" type="checkbox" name="data[Theme][<?php echo $theme['Theme']['themeId'] ?>][sites][]" value="family" <?php echo $checkedSite2; ?> />
            <?php //endif; ?>
            <span class="themeName"><?php echo $theme['Theme']['themeName']; ?></span>
            <span class="themeId" style="color: #cccccc;">(#<?php echo $theme['Theme']['themeId']; ?>)</span>
            <br />
        <?php endforeach; ?>
    </div>
</fieldset>
<?php //if ($is_family): ?>
    <fieldset class="collapsible">
        <legend class="handle">Family</legend>
        <div class="collapsibleContent">
            <div class="input ageRanges">
                <label>Good For Ages</label>
                <ul class="optionList">
                    <?php
                    $ranges = array('less than 1' => 'Less than 1 year: Babies',
                        '1-4' => '1 - 4 years: Toddlers',
                        '5-11' => '5 - 11 years: School Age',
                        '12-18' => '12 - 18 years: Preteens &amp; Teens');
                    foreach ($ranges as $value => $label):
                        if (!empty($this->data['Client']['ageRanges'])) {
                            $checked = (in_array($value, $this->data['Client']['ageRanges'])) ? ' checked' : '';
                        }
                        else {
                            $checked = '';
                        }
                        ?>
                        <li><input type="checkbox" name="data[Client][ageRanges][]" value="<?php echo $value; ?>"<?php echo $checked; ?>> <?php echo $label; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php foreach($this->data['ClientSiteExtended'] as $site) {
                if ($site['siteId'] == 2) {
                    echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.familiesShouldKnow', array('value' => $site['familiesShouldKnow']));
                }
            } ?>
        </div>
    </fieldset>
<?php //endif; ?>


<script>
    jQuery(function($) {
        $("#DestinationSelector").change(function(){
            $.getJSON("/destinations/get_parent_tree",{id: $(this).val()}, function(data) {
                $("#destinationRelDisplay").html('<img src="/img/spinner.gif">');
                var did = new Array();
                var dname = new Array();
                var dcount = 0;
                for (d in data.tree) {
                    if (data.tree.hasOwnProperty(d)) {
                        did[dcount] = data.tree[d].destinationId;
                        dname[dcount] = data.tree[d].destinationName;
                        dcount++;
                    }
                }
                $("#destinationIds").val(did.join(','));
                $("#destinationRelDisplay").html(dname.join('<br />'));
            })
        });
    });
</script>


<fieldset class="collapsible">
    <legend class="handle">Destinations <?=$html2->c($client['ClientDestinationRel']); ?></legend>
    <div class="collapsibleContent">
        <div class='controlset2'>

            <? $destSelectedArray = (isset($destSelected) && is_array($destSelected)) ? $destSelected : array(); ?>

            <input id="destinationIds" name="data[destinationIds]" value="<?= implode(',', $destSelectedArray); ?>" type="hidden">
            <div style="font-weight:bold; margin:0; padding:5px 0 5px;">Set Primary Destination:</div>
            <select id="DestinationSelector" name="data[Client][primaryDestinationId]" style="font-size:12px">
                <option value="">-- </option>
                <?= $destinationSelectOptions; ?>
            </select>
            <div id="destinationRelDisplay" style="font-size: 14px; margin:0; padding:10px 0 10px; line-height: 20px;">
                <?php foreach ($destSelectedArray as $d) { ?>
                    <?= $destinations[$d]; ?><br />
                <?php } ?>
            </div>
        </div>
    </div>
</fieldset>

<?  // jwoods 06/29/11 - removed IMAGES section; ?>

<fieldset class="collapsible">
    <legend class="handle">Tracking Links</legend>
    <div class="collapsibleContent">

        <p style="font-size:11px; line-height:15px; margin:10px 0px; font-style:italic;">The client's <b>URL</b> and <b>Check Rate URL</b> at the top of this page are the default URL and are used for display.<br/>Specifying the tracking links below will only replace the default link for that element (e.g. logo, check rates, visit website, etc.).</p>

        <?
        echo $form->input('ClientTracking.1.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">Main Logo</span>'));
        echo $form->input('ClientTracking.1.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">Main Logo</span>'));
        echo $form->input('ClientTracking.1.clientTrackingTypeId', array('value' => 1, 'type' => 'hidden'));
        echo $form->input('ClientTracking.1.clientTrackingId', array('type' => 'hidden'));

        ?><br/><?

        echo $form->input('ClientTracking.7.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">Check Rates</span>'));
        echo $form->input('ClientTracking.7.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">Check Rates</span>'));
        echo $form->input('ClientTracking.7.clientTrackingTypeId', array('value' => 7, 'type' => 'hidden'));
        echo $form->input('ClientTracking.7.clientTrackingId', array('type' => 'hidden'));

        ?><br/><?

        echo $form->input('ClientTracking.3.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">&quot;Visit Website&quot;</span>'));
        echo $form->input('ClientTracking.3.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">&quot;Visit Website&quot;'));
        echo $form->input('ClientTracking.3.clientTrackingTypeId', array('value' => 3, 'type' => 'hidden'));
        echo $form->input('ClientTracking.3.clientTrackingId', array('type' => 'hidden'));

        ?><br/><?

        echo $form->input('ClientTracking.4.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">Name in Description</span>'));
        echo $form->input('ClientTracking.4.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">Name in Description</span>'));
        echo $form->input('ClientTracking.4.clientTrackingTypeId', array('value' => 4, 'type' => 'hidden'));
        echo $form->input('ClientTracking.4.clientTrackingId', array('type' => 'hidden'));
        ?>

    </div>
</fieldset>

<fieldset class="collapsible">
    <legend class="handle">Client Interviews</legend>
    <div class="collapsibleContent">
        <?php

        echo $form->input('ClientInterview.0.intervieweeName', array('label' => 'Interview Headline'));
        echo $form->input('ClientInterview.0.article', array('label' => 'Interview Full Article', 'type' => 'textarea'));
        // echo $form->input('ClientInterview.0.summary', array('label' => 'Interview Summary', 'type' => 'textarea'));
        echo $form->input('ClientInterview.0.active', array('label' => 'Display Interview', 'type' => 'checkbox'));
        echo $form->input('ClientInterview.0.clientId', array('type' => 'hidden'));
        echo $form->input('ClientInterview.0.clientInterviewId', array('type' => 'hidden'));

        ?>
    </div><!-- close collapsibleContent -->
</fieldset>

<fieldset class="collapsible">
    <legend class="handle">Tags</legend>
    <div class="collapsibleContent">

        <script>
            jQuery(function() {
                var $ = jQuery;
                $("#tags-accordion").accordion({
                    collapsible: true,
                    active: false,
                    autoHeight: false
                });
            });
        </script>
        <div id="tags-accordion">
            <?php foreach ($tagGroups as $groupName => $tags): ?>

                <h3><a href="#"><?php echo $groupName; ?></a></h3>
                <div>
                    <table border="0">
                        <?php $tagCount = 0; ?>
                        <?php foreach($tags as $tagId => $tagName): ?>
                            <?php $tagCount++; ?>
                            <?php $bgcolor = ($tagCount % 2) ? '#eee' : '#fff'; ?>
                            <tr bgcolor="<?php echo $bgcolor; ?>">
                                <td style="width: 300px;"><?= $tagName; ?></td>
                                <?php $checked = (in_array($tagId, $selectedTags)) ? ' checked' : ''; ?>
                                <td style="width: 100px;" align="center">
                                    <input
                                        type="checkbox"
                                        name="data[ClientTagRel][<?php echo $tagId; ?>]"
                                        value="<?php echo $tagId; ?>"
                                        <?php echo $checked; ?>
                                        />
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</fieldset>


<!-- START social-->

<fieldset class="collapsible">
    <legend class="handle">Social Media</legend>
    <div class="collapsibleContent">





        <table class="socialmedia">
        <tr><th></th><th>Enabled</th><th>URL</th><th>Stats</th>
        </tr>
        <tr>
            <th>Facebook</th>
            <td><?php echo $form->input('ClientSocial.showFb', array('label' => '','type'=>'checkbox'));?></td>
            <td><?php echo $form->input('ClientSocial.fbUrl', array('label' => 'Facebook Page URL <a class="testfb" style="font-size:10px;">[Check]</a>', 'type' => 'text'));?>
                <div class="fb-url-test-results" style="display:inline;font-color:color: #00529B;"></div></td>
            <td>
                <?php
                if (isset($facebookStatsFirst['clientFacebookStats']['likeCount'])){


                ?>
                <table>
                    <tr><th>Initial</th><th>Latest</th></tr>
                    <tr>
                        <td>
                            <?
                            if(isset($facebookStatsFirst)){
                                echo 'Fans: '.$facebookStatsFirst['clientFacebookStats']['likeCount']."<br>\n";
                                echo 'Recorded: <i>'.date('Y-m-d H:i:s', strtotime($facebookStatsFirst['clientFacebookStats']['timestamp']))."</i>\n";
                            }
                            ?>

                        </td>
                        <td>
                            <?
                            if(isset($facebookStatsLatest)){
                                echo 'Fans: '.$facebookStatsLatest['clientFacebookStats']['likeCount']."<br>\n";
                                echo 'Recorded: <i>'.date('Y-m-d H:i:s', strtotime($facebookStatsLatest['clientFacebookStats']['timestamp']))."</i>\n";
                            }
                            ?>



                        </td>
                    </tr>


                </table>
                <?
                }//end hide FB block of no count
                ?>

            </td>

        </tr>
            <tr style="border-top:1px solid #CCC;">
                <th>Twitter</th>
                <td><?php echo $form->input('ClientSocial.showTw', array('label' => '','type'=>'checkbox')); ?></td>
                <td><?php echo $form->input('ClientSocial.twitterUser', array('label' => 'Twitter Username <a class="testtwitter" style="font-size:10px;">[Check]</a>', 'type' => 'text')); ?>
                    <div class="tw-user-test-results" style="display:inline;"></div>
                </td>
                <td >
                    <?php
                        if (isset($twitterStatsFirst['clientTwitterStats']['twitterUser'])){


                    ?>
                    <table><caption></caption></caption>
                        <tr><th>Initial</th><th>Latest</th></tr>
                        <tr><td><?
                            if(isset($twitterStatsFirst)){
                                echo 'Followers: '.$twitterStatsFirst['clientTwitterStats']['followersCount']."<br>\n";
                                echo 'Following: '.$twitterStatsFirst['clientTwitterStats']['friendsCount']."<br>\n";
                                echo 'Listed: '.$twitterStatsFirst['clientTwitterStats']['listedCount']."<br>\n";
                                echo 'Recorded: <i>'.date('Y-m-d H:i:s', strtotime($twitterStatsFirst['clientTwitterStats']['timestamp']))."</i>\n";

                            }

                            ?></td>
                        <td>
                            <?
                            if(isset($twitterStatsLatest)){
                                echo 'Followers: '.$twitterStatsLatest['clientTwitterStats']['followersCount']."<br>\n";
                                echo 'Following: '.$twitterStatsLatest['clientTwitterStats']['friendsCount']."<br>\n";
                                echo 'Listed: '.$twitterStatsFirst['clientTwitterStats']['listedCount']."<br>\n";
                                echo 'Recorded: <i>'.date('Y-m-d H:i:s', strtotime($twitterStatsLatest['clientTwitterStats']['timestamp']))."</i>\n";
                            }
                            ?>

                        </td></tr>
                    </table>
                    <?php
                        }//end hide twitter
                    ?>



                </td>
            </tr>
        </table>
            <script type="text/javascript">
                (function($) {

                    $(document).ready(function() {

                        $(".testfb").click(function() {
                            var fb_url = $('#ClientSocialFbUrl').val();


                            if(typeof fb_url === 'undefined'){

                                alert('Facebook page cannot be checked with blank field');
                                return false;
                            };

                            $(function() {
                                $(".fb-url-test-results").html('<img src="/img/spinner.gif">');
                                $.ajax({
                                    type: "GET",
                                    //url: location.hostname + "clients/testurl",
                                    url: "<?php echo $this->webroot; ?>clients/testurl",
                                    data: "checkurl=" + encodeURI(fb_url),
                                    success: function(data, textStatus) {
                                        alert(data);
                                        $(".fb-url-test-results").empty();
                                        $(".fb-url-test-results").html(data);
                                        $(".fb-url-test-results").css('background-color', '#BDE5F8');
                                    },
                                    error: function() {
                                        alert('Could not check URL. Try again.');
                                    }
                                });
                            });
                        });//end click fnc
                    });

                })(jQuery);


                (function($) {

                    $(document).ready(function() {

                        $(".testtwitter").click(function() {

                            var tw_user = $('#ClientSocialTwitterUser').val();

                                if(typeof tw_user === 'undefined'){

                                    alert('Twitter user cannot be checked with blank field');
                                    return false;
                                };
                            var twitter_url = 'https://twitter.com/'+tw_user;

                            $(function() {
                                $(".tw-user-test-results").html('<img src="/img/spinner.gif">');
                                $.ajax({
                                    type: "GET",
                                    //url: location.hostname + "clients/testurl",
                                    url: "<?php echo $this->webroot; ?>clients/testurl/twitter",
                                    data: "checkurl=" + encodeURI(twitter_url),
                                    success: function(data, textStatus) {
                                        alert(data);
                                        $(".tw-user-test-results").empty();
                                        $(".tw-user-test-results").html(data);
                                        $(".tw-user-test-results").css('background-color', '#BDE5F8');
                                    },
                                    error: function() {
                                        alert('Could not check URL. Try again.');
                                    }
                                });
                            });
                        });//end click fnc
                    });
                })(jQuery);

            </script>
            <? echo $form->input('ClientSocial.clientId', array('type' => 'hidden','value'=>$clientId));
            echo $form->input('ClientSocial.clientSocialId', array('type' => 'hidden'));
            ?>


    </div><!-- close collapsibleContent -->
</fieldset>
<!-- END social -->
<br>
</fieldset>




<?php echo $form->end('Submit');?>

</div>
