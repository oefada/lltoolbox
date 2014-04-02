<?php
if (!$package['Package']['maxAdults']) {
	$package['Package']['maxAdults'] = 0;
}
if (!$package['Package']['siteId']) {
	$package['Package']['siteId'] = 1;
}
if ($isFamilyPackage== true){
    $isFamilyPackage = 1;
}else{
    $isFamilyPackage = 0;
}
?>
<?php $this->layout = 'overlay_form'; ?>
<script type="text/javascript">
    var packageId = <?=$packageId;?>;
    var clientId = <?=$clientId;?>;
	var numNights = <?=(int)$package['Package']['numNights'];?>;
	var numGuests = <?=(int)$package['Package']['numGuests'];?>;
	var roomGrade = '<?=htmlentities($roomGrade);?>';
	var roomNightDescription = "<?=htmlentities($roomNightDescription);?>";
	var maxAdults = <?=(int)$package['Package']['maxAdults'];?>;
	var siteId = <?=(int)$package['Package']['siteId'];?>;
    var isFamilyPackage = <?=$isFamilyPackage; ?>;
	
	<?php if ($isFamilyPackage == 1) :?>
	var rangeLow = <?=(int)$package['PackageAgeRange']['rangeLow'];?>;
	var rangeHigh = <?=(int)$package['PackageAgeRange']['rangeHigh'];?>;
	<?php endif;?>
</script>

<?php echo $html->css('jquery.autocomplete'); 
echo $javascript->link('jquery/jquery-autocomplete/jquery.autocomplete'); ?>
<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>

<form id="edit_publishing">
	<h3 style="margin-bottom;15px;">Publishing</h3>
	<input type="hidden" name="data[Package][packageId]" value="<?=$package['Package']['packageId'];?>" />
	<table id="tbl-publishing" class="package">
	<tr><th>Package Title</th><td><input type="text" name="data[Package][packageTitle]" value="<?=htmlentities($packageTitle);?>" /></td></tr>
	<tr><th>Short Blurb</th><td><input type="text" name="data[Package][shortBlurb]" value="<?=htmlentities($package['Package']['shortBlurb']);?>" maxlength="65" /></td></tr>
	<tr><th>Package Blurb</th><td><input type="text" name="data[Package][packageBlurb]" value="<?=htmlentities($package['Package']['packageBlurb']);?>" maxlength="62" /></td></tr>
	<tr><th>Room Grade</th><td><input type="text" name="data[Package][roomGrade]" value="<?=htmlentities($roomGrade);?>" readonly="readonly" /></td></tr>
    <?php if (!$isMultiClientPackage): ?>
        <tr><th>Order Inclusions</th>
            <td>
    
            <?php foreach ($items as $k => $i):?>
                <?php if (isset($i['Group'])) :?>
                    <?php foreach ($i['LoaItem'] as $l => $j) :?>
                        <span style="display:none;" id="merch-desc-<?=$k.$l;?>-copy" rel="werd"><?=$j;?></span>
                        <input type="hidden" id="merch-desc-<?=$k.$l;?>-id" value="" />
                    <?php endforeach;?>
                <?php else: ?>
                    <span style="display:none;" id="merch-desc-<?=$k;?>-copy" rel="werd"><?=(!empty($i['LoaItem']['merchandisingDescription'])) ? htmlentities($i['LoaItem']['merchandisingDescription'])  : 'NO LIVE SITE DESCRIPTION'  ;?></span>
                    <input type="hidden" id="merch-desc-<?=$k;?>-id" value="<?=$i['PackageLoaItemRel']['packageLoaItemRelId'];?>" />
                <?php endif;?>
            <?php endforeach;?>
                <input type="hidden" name="data[PackageAgeRange][packageAgeRangeId]" value="<? echo $package['PackageAgeRange']['packageAgeRangeId'];?>">
                <input type="hidden" name="data[PackageAgeRange][packageId]" value="<? echo $packageId;?>">
                <input type="hidden" name="data[PackageAgeRange][rangeHigh]" value="<? echo $package['PackageAgeRange']['rangeHigh'];?>">
                <input type="hidden" name="data[PackageAgeRange][rangeLow]"  value="<? echo $package['PackageAgeRange']['rangeLow'];?>">
    
            <ul id="sortable">
            <?php foreach ($items as $k => $i):?>
                <?php if (isset($i['Group'])) :?>
                    <?php foreach ($i['LoaItem'] as $l => $j) :?>
                    <li class="ui-state-default" id="merch-desc-<?=$k.$l;?>"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><?=$j;?></li>
                    <?php endforeach;?>
                <?php else: ?>
                    <li class="ui-state-default" id="merch-desc-<?=$k;?>"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><?=(!empty($i['LoaItem']['merchandisingDescription'])) ? htmlentities($i['LoaItem']['merchandisingDescription'])  : 'NO LIVE SITE DESCRIPTION'  ;?></li>
                <?php endif;?>
            <?php endforeach;?>
            </ul>
            <input type="button" value="Update Inclusions" onclick="updateInclusions('sortable');" />
    
            </td></tr>
    <?php endif; ?>
    <tr><th></th><td><span style="color: #7f0000; font-weight: bold;">* NOTE * For Promo info that you do not want the client to see, wrap the promo info with &lt;promo&gt; and &lt;/promo&gt;</span></td></tr>
	<tr><th>Inclusions</th><td>
		<textarea name="data[Package][packageIncludes]" id="package-validity-includes" <?php if (!$isMultiClientPackage): ?>readonly="readonly"<?php endif; ?>><?=htmlentities($package['Package']['packageIncludes']);?></textarea>
		<div>
			<input type="hidden" id="edit-this-validity-includes" name="data[Package][overridePackageIncludes]" value="0" />
            <?php if (!$isMultiClientPackage): ?>
                <a href="javascript:void(0);" onclick="return editThis('validity-includes');">Make Changes</a>
            <?php endif; ?>
		</div>
		</td></tr>
	<tr><th>Terms &amp; Conditions</th><td><textarea name="data[Package][termsAndConditions]"><?=htmlentities($package['Package']['termsAndConditions']);?></textarea></td></tr>
	<?php if (!$package['Package']['isTaxIncluded']):?>
	<tr><th>Taxes Not Included Text</th><td><input type="text" name="data[Package][taxesNotIncludedDesc]" value="<?=htmlentities($package['Package']['taxesNotIncludedDesc']);?>" /></td></tr>
	<?php endif;?>
    <tr><th>Additional Information</th><td><textarea name="data[Package][pubAdditionalInfo]" style="height: 75px !important;"><?=htmlentities($package['Package']['pubAdditionalInfo']);?></textarea></td></tr>
    <tr><th>Promo Callout</th><td><textarea name="data[Package][pubCallout]" style="height: 75px !important;"><?=htmlentities($package['Package']['pubCallout']);?></textarea></td></tr>
    
    
	
	<!--
	<tr><th>Seasonal Pricing (Buy Now Only)</th><td><textarea name="data[Package][additionalDescription]"><?=htmlentities($package['Package']['additionalDescription']);?></textarea></td></tr>
	<tr>
		<th>Validity Disclaimer</th>
		<td>
			<textarea name="data[Package][validityDisclaimer]" id="package-validity-disclaimer" readonly="readonly"><?=$package['Package']['validityDisclaimerText'];?></textarea>
			<div>
				<input type="hidden" id="edit-this-validity-disclaimer" name="data[Package][overrideValidityDisclaimer]" value="0" />
				<a href="javascript:void(0);" onclick="return editThis('validity-disclaimer');">Make Changes</a>
			</div>
		</td>
	</tr>
	-->

	</table>
	<input type="hidden" id="inclusion_id_order" name="data[Inclusions][order]" />
	<input type="button" value="Save Changes" onclick="submitForm('edit_publishing');" />

<style type="text/css">
	#sortable { list-style-type: none; margin: 0; padding: 0; width: 540px; }
	#sortable li { margin: 0 3px 3px 15px; padding: 0.4em; padding-left: 1.5em;font-size:10px; color:#444;}
	#sortable li span { position: absolute; margin-left: -1.3em; }
</style>
<script type="text/javascript">
	$(function() {
		$("#sortable").sortable();
		$("#sortable").disableSelection();
	});
	function editThis(id_txt) {
		var prompt_da_user = confirm('Are you sure you want to make changes? This will prevent the inclusions for this package from being automatically updated in the future.');
		if (prompt_da_user) {
			$('#package-' + id_txt).removeAttr('readonly');
			$('#edit-this-' + id_txt).val(1);
		} else {
			return false;
		}
	}
    
    //acarney 2011-01-12 -- remove this function after flexpacks launch and rename updateInclusionsNew to updateInclusions
    //updateInclusionsNew applies the new flexpacks autotext rules
    //acarney 2011-01-18 -- will remove this function post-launch
    function updateInclusionsOld(id) {
		var inclusions = '';
		var inclusion_ids = '';
		if (isFamilyPackage == 0) {
			if (numGuests > 2) {
				inclusions += "<p><b>This "+ numNights  +"-night package sleeps up to "+ numGuests +":</b></p>\n";
			} else {
				inclusions += "<p><b>This "+ numNights  +"-night package for "+ numGuests +" includes:</b></p>\n";
			}
		} else if (isFamilyPackage == 1) {
			inclusions += "<p><b>This "+ numNights  +"-night package sleeps up to "+ numGuests +":</b></p>\n";
			inclusions += "<ul>\n";
			if (maxAdults == numGuests) {
				inclusions += "    <li>Valid for all ages</li>\n";
			} else {
				inclusions += "    <li>Maximum "+ maxAdults +" adults</li>\n";
				inclusions += "    <li>Children ages "+ rangeLow +"-"+ rangeHigh +"</li>\n";
			}
			inclusions += "</ul><br>\n";
			inclusions += "<p><b>This package includes:</b></p>\n";
		}

		inclusions += "<ul>\n";
		inclusions += '    <li>'+ numNights  +' nights in '+ roomNightDescription +"</li>\n";
		var lis = $("#sortable li").each(function(i) {
			var merch = $('#' + this.id + '-copy').html();
			if (merch) {
				inclusions += '    <li>'+ merch +"</li>\n";
				inclusion_ids += $('#' + this.id + '-id').val() + ',';
			}
		});
		inclusions += '</ul>';

		
		$('#inclusion_id_order').val(inclusion_ids);
		$('#package-validity-includes').html(inclusions);
	}
        
	function updateInclusions(id) {
        var inclusions = '';
        var inclusion_ids = '';

        inclusions += "<p><b>Accommodations for " + numGuests + ":</b></p>\n";
        inclusions += "<ul>\n";

        if (isFamilyPackage == 1) {
            if (maxAdults == numGuests) {
                inclusions += "    <li>Valid for all ages</li>\n";
            } else {
                inclusions += "    <li>Maximum " + maxAdults + " adults</li>\n";
                inclusions += "    <li>Children ages " + rangeLow + "-" + rangeHigh + "</li>\n";
            }
        }
        inclusions += '    <li>' + roomNightDescription + "</li>\n";
        inclusions += "</ul><br>\n";
        inclusions += "<p><b>Included with this package:</b></p>\n";
        inclusions += "<ul>\n";

        var lis = $("#sortable li").each(function (i) {
            var merch = $('#' + this.id + '-copy').html();
            if (merch) {
                inclusions += '    <li>' + merch + "</li>\n";
                inclusion_ids += $('#' + this.id + '-id').val() + ',';
            }
        });
        inclusions += '</ul>';

        $('#inclusion_id_order').val(inclusion_ids);
        $('#package-validity-includes').html(inclusions);
	}
</script>

</form>
