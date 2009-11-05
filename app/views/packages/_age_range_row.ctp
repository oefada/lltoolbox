<div>
<?php
	if ($row == 0 && (!isset($data['rangeHigh']) && !isset($data['rangeLow']))) {
		$data['rangeLow'] = 0;
		$data['rangeHigh'] = 17;
	}
?>
<select name="data[PackageAgeRange][<?=$row?>][rangeLow]">
	<?php for($i = 0; $i <= 17; $i++): ?>
	<option value="<?=$i?>"<?php if($data['rangeLow'] == $i) {echo ' selected="selected"';}?> /><?=$i?></option>
	<?php endfor; ?>
</select>

<select name="data[PackageAgeRange][<?=$row?>][rangeHigh]">
	<?php for($i = 1; $i <= 17; $i++): ?>
	<option value="<?=$i?>"<?php if($data['rangeHigh'] == $i) {echo ' selected="selected"';}?> /><?=$i?></option>
	<?php endfor; ?>
</select>

<? if ($row > 0): ?>
<a href="#" onclick="return false;"  class="ageRangeDel"><img src="/img/edit_remove.png" style="margin-bottom: -5px; "/></a>
<? endif; ?>
</div>