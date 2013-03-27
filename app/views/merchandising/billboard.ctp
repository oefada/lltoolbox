<style type="text/css">
.input { margin-bottom: 10px; }
table { border: none; }
table label { float: left; }
table tr { margin-bottom: 5px; }
table tr th { text-decoration: underline; }
table tr td { border: none; }
.input { margin: 0; }
.input-row { border-bottom: 1px dashed black; }
.input-row td { padding: 25px 0 25px; }
.input-col label { float: left; width: 80px; text-align: right; margin-right: 10px; margin-bottom: 4px; margin-top: 2px; clear: left; }
.input-col input { float: left; width: 350px; margin-bottom: 4px; }
.date { width: 350px; float: left; }
form div { clear: none; }
</style>

<script type="text/javascript" src="/js/tablednd.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sort-table .input-row').each(function(index) {
		setRow('r' + (index+1));
	});
});

function addRow() {
	var row = jQuery('#sort-table .input-row:first').clone();
	var numRows = jQuery('#sort-table .input-row').length;
	// check id not existing already
	while (jQuery('#r'+(numRows+1)).length != 0) {
		numRows++;
	}
	row.attr('id', 'r'+(numRows+1));
	jQuery('#sort-table .input-row:last').after(row);
	jQuery('#r'+(numRows+1)+' .input-col').children('input').val('');
	jQuery('#r'+(numRows+1)+' .input-col').children('.image-preview').html('');
	jQuery('#r'+(numRows+1)+' .input-col').children('.image-url').val(jQuery('#add-image-url').val());
	jQuery('#add-image-url').val('');
	jQuery('#r'+(numRows+1)+' .remove-link').click(function() {
		if (jQuery('#sort-table .input-row').length > 0) {
			jQuery('#r'+(numRows+1)).remove();
		} else {
			alert('Must have at least one slide!');
		}
	});
	
	setRow('r'+(numRows+1));
}

function setRow(rowId) {
	jQuery('#sort-table #'+rowId+' .link-url').blur(function() {
		updatePreviewImg(rowId);
	});
	jQuery('#'+rowId+' .remove-link').click(function() {
		if (jQuery('#sort-table .input-row').length > 1) {
			jQuery('#'+rowId+'').remove();
		} else {
			alert('Must have at least one slide!');
		}
	});
	jQuery('#sort-table').tableDnD();
	updatePreviewImg(rowId);
}
jQuery(function() {
	jQuery('#scheduleDate').click(function() {
		showCalendar('scheduleDate', '%Y-%m-%d');
	});
});

function updatePreviewImg(rowId) {
	jQuery('#sort-table #'+rowId+' .image-preview').html('<img src="'+jQuery('#sort-table #'+rowId+' .image-url').val()+'" width="360" height="100" />');
	if (jQuery('#sort-table #'+rowId+' .link-url').val()) {
		jQuery.get('/merchandising/clientInfo/?linkUrl='+jQuery('#sort-table #'+rowId+' .link-url').val(), function(data) {
			var dataArr = jQuery.parseJSON(data);
			if (dataArr.clientId) {
				jQuery('#sort-table #'+rowId+' .link-text').val('View Experience');
				jQuery('#sort-table #'+rowId+' .headline').val(dataArr.name);
				jQuery('#sort-table #'+rowId+' .description').val(dataArr.locationDisplay);
				jQuery('#sort-table #'+rowId+' .image-alt').val(dataArr.name + ' ' + dataArr.locationDisplay);
			}
		});
	}
}
</script>


<h3 style="font-size: 16px; padding: 0;">Billboard</h3>
<br />

<div style="float: left; margin: 5px 0 0 15px;">
	Select Site:
</div>
<div style="float: left; margin-left: 10px;">
	<select>
		<option>Luxury Link</option>
		<option>Family Getaway</option>
	</select>
</div>
<div style="clear: both;"></div>
<br />


<h2>Welcome Slide</h2>

<div style="float: left; margin-top: 25px; margin-left: 15px;">
	<div style="width: 360px; height: 100px; border: 1px solid black;">
		<? if (isset($welcomeSlideData['imageUrl']) && !empty($welcomeSlideData['imageUrl'])) : ?>
		<img src="<?=$welcomeSlideData['imageUrl']?>" width="360" height="100" />
		<? endif; ?>
	</div>
</div>
<div style="float: left;">

	<form name="welcomeSlide" method="POST" action="/merchandising/billboard">
	<input type="hidden" name="welcomeSlide" value="1" />
	<fieldset>
		<div class="input">
			<label>*Image URL</label>
			<input type="text" name="imageUrl" value="<?=@$welcomeSlideData['imageUrl']?>" />
		</div>
		<div class="input">
			<label>
				Link URL
				<? if (isset($welcomeSlideData['clientId'])) echo ' (cid: '.$welcomeSlideData['clientId'].')'; ?>
			</label>
			<input type="text" name="linkUrl" value="<?=@$welcomeSlideData['linkUrl']?>" />
		</div>
		<div class="input">
			<label>Link Text</label>
			<input type="text" name="linkText" value="<?=@$welcomeSlideData['linkText']?>" />
		</div>
        <div class="input">
            <label>Alt Tag</label>
            <input type="text" name="imageAlt" class="image-alt" value="<?=@$welcomeSlideData['imageAlt']?>" />
        </div>
        <div class="input">
			<label>Headline</label>
			<input type="text" name="headline" value="<?=@$welcomeSlideData['headline']?>" />
		</div>
		<div class="input">
			<label>Description</label>
			<input type="text" name="description" value="<?=@$welcomeSlideData['description']?>" />
		</div>
	</fieldset>
	<div style="float: right;">
		<input type="submit" value="Save" />
	</div>
	
	</form>
</div>
<div style="clear: both;"></div>

<br />
<h2>Slideshow Scheduling</h2>




<form name="schedule-date" method="POST" action="/merchandising/billboard">
	<input type="hidden" name="schedule-date" value="1" />
	<?=$datePicker->picker('scheduleDate', array('label' => 'Select a date to schedule: ','value'=>(isset($scheduleDate)?$scheduleDate:'')));?>
	<div style="float: left;">
		<input type="submit" value="Go" />
	</div>
</form>


<div style="float: left; margin-left: 35px;">
<? if (isset($others['current']['startDate'])) : ?>
Currently scheduled date: <?=$others['current']['startDate'];?><br />
<? endif; ?>
<? if (isset($others['next']['startDate'])) : ?>
Next scheduled date: <?=$others['next']['startDate'];?><br />
<? endif; ?>
</div>
<? if (isset($scheduleDate)) : ?>
<div style="float: left; margin-left: 35px;">
	<input type="submit" value="Preview" onClick="window.open('http://www.luxurylink.com/?pDate=<?=$scheduleDate?>'); return false;" />
</div>
<? endif; ?>
<div style="clear: both;"></div>

<? if (!isset($scheduleDate)) : ?>
	<center><h3>Please select a date to schedule</h3></center>
	<br /><br />
<? else : ?>
	<? if (isset($dataSaved) && $dataSaved) : ?>
	<center><h3>Changes have been saved</h3></center>
	<? endif; ?>
<span style="font-size: 14px;">Scheduling slides for <?=$scheduleDate?></span>
<br /><br />

<form name="slides" method="POST" action="/merchandising/billboard">
<input type="hidden" name="slides" value="1" />
<input type="hidden" name="data[scheduleDate]" value="<?=$scheduleDate?>" />
<table id="sort-table">
	<tr id="header-row" class="nodrag nodrop">
		<th>Image Preview</th>
		<th>Property Details</th>
		<th>Remove</th>
	</tr>
	
	<?
	if (isset($currData) && is_array($currData)) :
		$i = 0;
		foreach ($currData AS $slide) :
		$i++;
	?>
	<tr id="r<?=$i?>" class="input-row">
		<td>
			<div class="image-preview" style="width: 360px; height: 100px; border: 1px solid black;">
				<? if (isset($slide['imageUrl'])) : ?>
				<img src="<?=@$slide['imageUrl']?>" width="360" height="100" />
				<? endif; ?>
			</div>
		</td>
		<td class="input-col">
			<label>*Image URL</label>
			<input type="text" name="imageUrl[]" class="image-url" value="<?=@$slide['imageUrl']?>" />
			
			<label>Link URL</label>
			<input type="text" name="linkUrl[]" class="link-url" value="<?=@$slide['linkUrl']?>"/>
			
			<label>Link Text</label>
			<input type="text" name="linkText[]" class="link-text" value="<?=@$slide['linkText']?>" />
			
			<label>Alt Tag</label>
			<input type="text" name="imageAlt[]" class="image-alt" value="<?=@$slide['imageAlt']?>" />
			
			<label>Headline</label>
			<input type="text" name="headline[]" class="headline" value="<?=@$slide['headline']?>" />
			
			<label>Description</label>
			<input type="text" name="description[]" class="description" value="<?=@$slide['description']?>" />
		</td>
		<td><center><a class="remove-link"><img src="/img/x.png"></a></center></td>
	</tr>
	<? 
		endforeach;
	else :
	?>
	
	<tr id="r1" class="input-row">
		<td>
			<div class="image-preview" style="width: 360px; height: 100px; border: 1px solid black;">

			</div>
		</td>
		<td class="input-col">
			<label>*Image URL</label>
			<input type="text" name="imageUrl[]" class="image-url" />
			
			<label>Link URL</label>
			<input type="text" name="linkUrl[]" class="link-url" />
			
			<label>Link Text</label>
			<input type="text" name="linkText[]" class="link-text" />
			
			<label>Alt Tag</label>
			<input type="text" name="imageAlt[]" class="image-alt" />
			
			<label>Headline</label>
			<input type="text" name="headline[]" class="headline" />
			
			<label>Description</label>
			<input type="text" name="description[]" class="description" />
		</td>
		<td><center><a class="remove-link"><img src="/img/x.png"></a></center></td>
	</tr>
	<? endif; ?>
	
	
	<tr class="nodrag nodrop">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input type="submit" value="Save" /></td>
	</tr>
</table>
</form>

<br />
Add New Slide:
<br />
<div style="float: left;">
	<fieldset>
		<div class="input">
			<label>Image URL</label>
			<input id="add-image-url" type="text" />
		</div>
	</fieldset>
</div>
<div style="float: left; margin-top: 5px;">
	<input type="submit" value="Add" onclick="addRow(); return false;" />
</div>

<? endif; ?>
