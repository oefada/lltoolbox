<?php   echo $html->css('jquery.autocomplete'); 
        echo $javascript->link('jquery/jquery-autocomplete/jquery.autocomplete');
        //echo $javascript->link('jquery/jquery-ui-1.7.1.custom.min');
?>
<script type="text/javascript">
    $().ready(function() {
        init_ajax();

        $('div.add-variation').click(function() {
                                            var sectionId = $($(this).prev('div')).attr('id').split('_')[1];
                                            $.get('/mailings/addVariationToSection/<?php echo $mailing['Mailing']['mailingId']; ?>/'+sectionId,
                                                    function(data) {
                                                        $('div#variations_'+sectionId).append(data);
                                                        init_ajax();
                                                        var variationId = $($('div#variations_'+sectionId).children('div').children('ul.sortable')).last().attr('id').split('-')[1];
                                                        init_new_variation_picker(sectionId, variationId);
                                                    }
                                                );
                                    }
                                );
        $('input.freeze-list').click(function() {
                                            $.post('/mailings/addClients',
                                                   $(this).parent('form').serialize(),
                                                   function(targetElem) {
                                                        return function(data) {
                                                            $(targetElem).parent('form').parent().parent('div.variations').html(data);
                                                            init_ajax();
                                                        }
                                                   }(this)
                                            );
                                    }
                                );
        $('input.save-ad').click(function() {
                                        $.post('/mailings/saveAd',
                                               $(this).parent().parent('form').serialize(),
                                               function(button) {
                                                    return function(data) {
                                                        response = $.parseJSON(data);
                                                         $(button).next('div.ad-image').html(response['imagePath']);
                                                         $(button).parent().parent('form').find('input.mailingAdvertisingId').attr('value', response['newAdId']);
                                                    }
                                               } (this)
                                        );
                                    }
                                );     
        }
    );
    
    function init_ajax() {
        init_sortables();
        init_client_pickers();
        init_add_client_buttons();
        init_delete_buttons();
    }
    
    function init_sortables() {
        $('ul.sortable').sortable({
            handle: '.handle',
            update: function() {
                        var order = $(this).sortable('serialize');
                        $(this).load('/mailings/setSortOrder?'+order, function() {
                                            init_delete_buttons();
                                    });
                        }
                }
        );
    }
    
    function init_new_variation_picker(section, variation) {
        $('input#picker_'+section+'-'+variation).autocomplete('/mailings/suggestClients/<?php echo $mailing['Mailing']['mailingId']; ?>',
                                                        { extraParams: { sectionId: section,
                                                                         variationId: variation}
                                                        }
                                                 );
        $('input#picker_'+section+'-'+variation).result(function(event, data, formatted) {
                                            if (data) {                                                
                                              $(this).next('input').val(data[1]);
                                             }
                            }
                        );
    }
    
    function init_client_pickers() {
        <?php foreach ($mailing['MailingType']['MailingSection'] as $section): ?>
                <?php if (empty($section['Variations'])): ?>
                            $('input#picker_<?php echo $section['mailingSectionId']; ?>-A').autocomplete('/mailings/suggestClients/<?php echo $mailing['Mailing']['mailingId']; ?>',
                                                                                                            { extraParams: { sectionId: <?php echo $section['mailingSectionId']; ?>,
                                                                                                                             variationId: 'A' }
                                                                                                            }
                                                                                         );
                            $('input#picker_<?php echo $section['mailingSectionId']; ?>-A').result(function(event, data, formatted) {
                                                                if (data) {                                                
                                                                  $(this).next('input').val(data[1]);
                                                                 }
                                                }
                                            );
                <?php else: ?>
                    <?php foreach($section['Variations'] as $variationId => $clients): ?>
                            $('input#picker_<?php echo $section['mailingSectionId']; ?>-<?php echo $variationId; ?>').autocomplete('/mailings/suggestClients/<?php echo $mailing['Mailing']['mailingId']; ?>',
                                                                                                                                    { extraParams: { sectionId: <?php echo $section['mailingSectionId']; ?>,
                                                                                                                                                     variationId: '<?php echo $variationId; ?>' }
                                                                                                                                    }
                                                                                                                 );
                            $('input#picker_<?php echo $section['mailingSectionId']; ?>-<?php echo $variationId; ?>').result(function(event, data, formatted) {
                                                                if (data) {                                                
                                                                  $(this).next('input').val(data[1]);
                                                                 }
                                                }
                                            );
                    <?php endforeach; ?>
                <?php endif; ?>
        <?php endforeach; ?>
    }
    
    function init_add_client_buttons() {
         $('input.add-button').unbind('click');
         $('input.add-button').click(function() {
                                            $.post('/mailings/addClients',
                                                    $(this).parent('form').serialize(),
                                                    function(targetElem) {
                                                        return function(data) {
                                                            $(targetElem).parent().prev('ul').append(data);
                                                            $($(targetElem).siblings('input.client-picker')).val('');
                                                            if ($(targetElem).parent().prev('ul').children().size() == $($(targetElem).siblings('.maxInsertions')).val()) {
                                                                $(targetElem).attr('disabled', true);
                                                                $($(targetElem).siblings()).attr('disabled', true);
                                                            }
                                                            init_delete_buttons();
                                                        }
                                                    }(this)
                                            );
                                        }
                                  );
    }
    
    function init_delete_buttons() {
        $('img.delete-item').unbind('click');
        $('img.delete-item').click(function() {
                    if (confirm('Are you sure you want to delete '+$(this).parent('li').text()+' from this variation?')) {
                        var deleteId = $(this).parent('li').attr('id').split('_')[1];
                        $.get('/mailings/deleteFromVariation/'+deleteId,
                                                function(targetElem) {
                                                    return function(data) {
                                                        var list = $(targetElem).parent().parent('ul');
                                                        var deleteElem = '#listItem_'+data;
                                                        $(deleteElem).remove();
                                                        var formElem = $(list).siblings('form');
                                                        if ($(list).children().size() < $($(formElem).children('.maxInsertions')).val()) {
                                                            $($(formElem).children()).attr('disabled', false);
                                                        }
                                                    }
                                                }(this)
                        );
                    }
           }
       );
    }
    
    </script>

<style>
    .mailings h3 {
        font-size:14px;
    }
    
    .section {
        border-bottom:1px dotted #666;
        padding-bottom:10px;
        position:relative;
    }
    
    div.variations {
        margin:10px;
        min-height:100px;
    }
    
    .variations h4 {
        font-weight:bold;
        font-size:12px;
        text-align:center;
        padding:0 0 10px 0;
        color:#666;
        width:175px;
    }
    
    .variations p {
    }
    
    input, input.add-button {
        display:inline;
        padding:2px;
        margin-top:10px;
    }
    
    div.add-variation {
        clear:both;
        width:95px;
        padding:10px;
        text-decoration:underline;
        color:#336699;
        cursor:pointer;
    }
    
    div.variation-container {
        float:left;
        border:1px solid #e0e0e0;
        width:275px;
        min-height:100px;
        padding:10px;
        margin:10px;
    }
    
    ul.sortable {
        list-style-type:none;
    }
    
    ul.sortable li {
        position:relative;
        padding: 2px 0 2px 0;
    }
    
    ul.sortable li img {
        padding:3px;
        vertical-align:middle;
    }
    
    ul.sortable li img.delete-item {
        position:absolute;
        right:0;
        cursor:pointer;
    }
    
    div.ad-image {
        float:right;
        height:250px;
        margin-left:300px;
        margin-top:-100px;
    }
    
    div.mailing-ad {
        float:left;
    }
    
    div.marketplace-item {
        padding-top:20px;
        padding-bottom:20px;
        width:600px;
    }
    
    div.item-field {
        float:left;
        width:500px;
    }
    
    div.item-field div {
        height:20px;
        float:left;
    }
    
    div.item-field input {
        width:300px;
        float:right;
    }
    
    div.marketplace-item textarea {
        margin-top:10px;
        width:300px;
        height:100px;
        float:right;
    }
    
    div.marketplace-item img {
        float:right;
    }
    
</style>

<div class="mailings form">
	<fieldset>
 		<legend><?php __('Edit Newsletter');?></legend>
        <h2><?php echo $mailing['MailingType']['mailingTypeName'].' newsletter scheduled for '.date('D. M. j, Y', strtotime($mailing['Mailing']['mailingDate'])); ?></h2>
	<?php
        $sections = $mailing['MailingType']['MailingSection'];
        foreach ($sections as $section): ?>
            <div class="section">
                <h3>
                    <?php echo $section['mailingSectionName']; ?>
                    <?php if (!empty($section['mailingSectionContent'])) {
                            echo ' &ndash; Preview as of '.date('M. j, Y h:i a');
                    } ?>                
                </h3>
                <div id="variations_<?php echo $section['mailingSectionId']; ?>" class="variations">
                    <?php if (empty($section['Variations'])) {
                            echo $this->element('mailing_scheduler/client_picker', array('variationId' => 'A', 'mailingId' => $mailing['Mailing']['mailingId'], 'sectionId' => $section['mailingSectionId'], 'clients' => array(), 'maxInsertions' => $section['maxInsertions']));
                          }
                          else {
                            foreach($section['Variations'] as $variationId => $clients) {
                              echo $this->element('mailing_scheduler/client_picker', array('variationId' => $variationId, 'mailingId' => $mailing['Mailing']['mailingId'], 'sectionId' => $section['mailingSectionId'], 'clients' => $clients, 'maxInsertions' => $section['maxInsertions'], 'sectionContent' => $section['mailingSectionContent']));
                            }
                        }
                    ?>
                </div>
                <?php if (empty($section['mailingSectionContent'])): ?>
                    <div class="add-variation">Add a variation</div>
                <?php else: ?>
                    <div style="clear:both;">&nbsp;</div>
                <?php endif; ?>
            </div>
        <?php endforeach; 
	?>
    <?php if (in_array($userDetails['samaccountname'], $adusers) || in_array('Geeks', $userDetails['groups'])): ?>
        <div class="section">
                <?php for ($i = 0; $i < 2; $i++): ?>
                        <h3>Ad Image &#150; Section <?php echo $i + 1; ?></h3>
                        <form>
                            <div class="mailing-ad">
                                <strong>Enter path to ad image:</strong> <input type="text" size="30" name="data[MailingAdvertising][imageUrl]" value="<?php echo (!empty($mailing['Mailing']['300x250'][$i]['MailingAdvertising']['imageUrl'])) ? $mailing['Mailing']['300x250'][$i]['MailingAdvertising']['imageUrl'] : ''; ?>" /><br />
                                <strong>Alt text:</strong> <input type="text" size="44" name="data[MailingAdvertising][imageAlt]" value="<?php echo (!empty($mailing['Mailing']['300x250'][$i]['MailingAdvertising']['imageAlt'])) ? $mailing['Mailing']['300x250'][$i]['MailingAdvertising']['imageAlt'] : ''; ?>" /><br />
                                <strong>Image link:</strong> <input type="text" size="44" name="data[MailingAdvertising][linkUrl]" value="<?php echo (!empty($mailing['Mailing']['300x250'][$i]['MailingAdvertising']['linkUrl'])) ? $mailing['Mailing']['300x250'][$i]['MailingAdvertising']['linkUrl'] : ''; ?>" />
                                <input type="hidden" name="data[MailingAdvertising][mailingId]" value="<?php echo $mailing['Mailing']['mailingId']?>" />
                                <input type="hidden" name="data[MailingAdvertising][mailingAdvertisingTypeId]" value="1" />
                                <input type="hidden" name="data[MailingAdvertising][mailingSectionId]" value="<?php echo $i + 1; ?>" />
                                <input type="hidden" name="data[MailingAdvertising][mailingAdvertisingId]" class="mailingAdvertisingId" value="<?php echo (!empty($mailing['Mailing']['300x250'][$i]['MailingAdvertising']['mailingAdvertisingId'])) ? $mailing['Mailing']['300x250'][$i]['MailingAdvertising']['mailingAdvertisingId'] : ''; ?>" /><br />
                                <input type="button" class="save-ad" value="Save Ad for Section <?php echo $i + 1; ?>" />
                                <div class="ad-image">
                                    <?php if (!empty($mailing['Mailing']['300x250'][$i]['MailingAdvertising']['imageUrl'])): ?>
                                            <img src="<?php echo $mailing['Mailing']['300x250'][$i]['MailingAdvertising']['imageUrl']; ?>" />
                                    <?php else: ?>
                                            &nbsp;
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="clear:both">&nbsp;</div>
                        </form>
                    <?php endfor; ?>
        </div>
        <h3>Marketplace</h3>
        <div class="section">
            <form id="mailingMarketplace" method="post" action="/mailings/saveMarketplace">
                <?php foreach($mailing['MailingAdvertising'] as $i => $ad): ?>
                        <?php if (!empty($mailing['MailingAdvertising'][$i]) && $mailing['MailingAdvertising'][$i]['mailingAdvertisingTypeId'] != 2) {
                                continue;
                        } ?>
                        <?php if (empty($mailing['MailingAdvertising'][$i])) {
                                    $imageUrl = '';
                                    $imageAlt = '';
                                    $linkUrl = '';
                                    $linkText = '';
                                    $blurb = '';
                                }
                                else {
                                    $imageUrl = $mailing['MailingAdvertising'][$i]['imageUrl'];
                                    $imageAlt = $mailing['MailingAdvertising'][$i]['imageAlt'];
                                    $linkUrl = $mailing['MailingAdvertising'][$i]['linkUrl'];
                                    $linkText = $mailing['MailingAdvertising'][$i]['linkText'];
                                    $blurb = $mailing['MailingAdvertising'][$i]['blurb'];
                                }
                        ?>
                        <div class="marketplace-item">
                            <input type="hidden" name="data[MailingAdvertising][<?php echo $i; ?>][mailingId]" value="<?php echo $mailing['Mailing']['mailingId']; ?>" />
                            <input type="hidden" name="data[MailingAdvertising][<?php echo $i; ?>][sortOrder]" value="<?php echo $i + 1; ?>" />
                            <?php if (!empty($mailing['MailingAdvertising'][$i]['mailingAdvertisingId'])): ?>
                                    <input type="hidden" name="data[MailingAdvertising][<?php echo $i; ?>][mailingAdvertisingId]" value="<?php echo $mailing['MailingAdvertising'][$i]['mailingAdvertisingId'] ?>" />
                            <?php endif; ?>
                            <?php if ($mailing['Mailing']['mailingTypeId'] == 1): ?>
                                    <input type="hidden" name="data[MailingAdvertising][<?php echo $i; ?>][mailingSectionId]" value="1" />
                            <?php endif; ?>
                            <h3>Item <?php echo $i+1; ?></h3>
                            <div class="item-field"><div><strong>Enter path to image:</strong></div> <input type="text" name="data[MailingAdvertising][<?php echo $i; ?>][imageUrl]" value="<?php echo $imageUrl; ?>" /></div>
                            <?php if (!empty($mailing['MailingAdvertising'][$i]['imageUrl'])): ?>
                                    <img src="<?php echo $mailing['MailingAdvertising'][$i]['imageUrl']; ?>" />
                            <?php endif; ?>
                            <br />
                            <div class="item-field"><div><strong>Alt text:</strong></div> <input type="text" name="data[MailingAdvertising][<?php echo $i; ?>][imageAlt]" value="<?php echo $imageAlt; ?>" /></div>
                            <div class="item-field"><div><strong>Link path:</strong></div> <input type="text" name="data[MailingAdvertising][<?php echo $i; ?>][linkUrl]" value="<?php echo $linkUrl; ?>" /></div>
                            <div class="item-field"><div><strong>Link text:</strong></div> <input type="text" name="data[MailingAdvertising][<?php echo $i; ?>][linkText]" value="<?php echo $linkText; ?>" /></div>
                            <div class="item-field"><div><strong>Blurb:</strong></div> <textarea name="data[MailingAdvertising][<?php echo $i; ?>][blurb]" /><?php echo $blurb; ?></textarea></div>
                        </div>
                <?php endforeach; ?>
                <div style="clear:both;">&nbsp;</div>
                <input type="button" onclick="document.forms.mailingMarketplace.submit()" id="save-marketplace" value="Save Marketplace Items" />
            </form>
        </div>
    <?php endif; ?>
	</fieldset>
</div>
