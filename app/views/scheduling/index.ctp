<script>
function openSchedulingOverlay(date, packageId) {
	Modalbox.show("/scheduling_masters/add/packageId:"+packageId+"/date:"+$F(date));
}
var dp = datePickerController.datePickers["dp-normal-1"];
function gotoMonth()
{
	$('spinner').show();
	var date = $F('dp-normal-1');
	var dateArray = date.split('-');
	var url = '/scheduling/index/clientId:<?=$clientId?>/month:'+dateArray[1]+'/year:'+dateArray[0];
	window.location.replace( url );  
}
</script>
<h2 class='title'>Scheduling</h2>
<div id='sContainer'>
	<div style="text-align: right"></strong>KEY:</strong> Single<div class='oType1 key'></div> Fixed Price<div class='oType2 key'></div><strong class='textRed'>Go To:</strong>
	<input type="hidden" class="format-y-m-d divider-dash" id="dp-normal-1" name="dp-normal-1" value="<?=$year.'-'.$month.'-01'?>" maxlength="10" size='10' onchange='javascript: gotoMonth(); return false;' /></div>

	<h3><?=$monthYearString?></h3>
	
	<div id='cContainer' class='clearfix'>
		<?php echo $this->renderElement('../scheduling/_days'); ?>
		
		<div id='pContainer'>
			<?php
			$row = 1;
			foreach($packages as $package):
				echo $this->renderElement('../scheduling/_package_row', array('package' => $package, 'row' => $row++));
			endforeach;
			?>
		</div>
	</div>
</div>