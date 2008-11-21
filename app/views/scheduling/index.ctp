<?php $this->pageTitle = $clientName.$html2->c($clientId, 'Client Id:') ?>
<script>
function openSchedulingOverlay(date, packageId, packageName) {
	Modalbox.show("/scheduling_masters/add/packageId:"+packageId+"/date:"+$F(date), {title: 'Scheduling '+packageName});
}
var dp = datePickerController.datePickers["dp-normal-1"];
function gotoMonth(theLink, month)
{
	$('spinner').show();
	yearSelect = $(theLink).up().previous('.yearPickerDiv').down('.yearPicker');
	var url = '/scheduling/index/clientId:<?=$clientId?>/month:'+month+'/year:'+$F(yearSelect);
	window.location.replace( url );
}
</script>
<style>
#monthPickerDiv {
	width: 200px;
}
#monthPickerDiv div {
	float: left;
	clear: none;
	padding: 4px;
}
#monthPickerDiv div a{
	background: #545454;
	display: block;
	color: #fff;
	width: 30px;
	font-weight: bold;
	padding: 5px;
	text-align: center;
	text-decoration: none;
	border: 1px solid #fff;
}
#monthPickerDiv div a:hover {
	color: #7f0000;
	background: #bbccd0;
	border: 1px solid #fff;
}
#monthPickerDiv div.first {
	clear: left;
}
</style>
<h2 class='title'>Scheduling</h2>
<div id='sContainer'>
	<div style="text-align: right" class='oKeys'><strong>KEY:</strong> Single<div class='oType1 oKey1'></div> Fixed Price<div class='oType2 oKey2'></div><strong class='textRed'>Go To:</strong>
	<img src="/img/cal.png" id='monthPickerTarget' style='cursor: pointer' /></div>
    <div id="monthPickerDiv" class='clearfix' style='display: none'>
		<div style="clear: both; text-align: center; float: none" class='yearPickerDiv'>Year:
		<select name="yearPicker" class='yearPicker'>
		<?PHP for($i=date("Y")-3; $i<=date("Y")+3; $i++)
		if($year == $i)
			echo "<option value='$i' selected>$i</option>";
		else
			echo "<option value='$i'>$i</option>";
		?>
		</select>
		</div>
    	<div><a href='#' onclick='javascript: gotoMonth(this, 1)'>Jan</a></div>
		<div><a href='#' onclick='javascript: gotoMonth(this, 2)'>Feb</a></div>
		<div><a href='#' onclick='javascript: gotoMonth(this, 3)'>Mar</a></div>
		<div><a href='#' onclick='javascript: gotoMonth(this, 4)'>Apr</a></div>
		<div class='first'><a href='#' onclick='javascript: gotoMonth(this, 5)'>May</a></div>
		<div><a href='#' onclick='javascript: gotoMonth(this, 6)'>Jun</a></div>
		<div><a href='#' onclick='javascript: gotoMonth(this, 7)'>Jul</a></div>
		<div><a href='#' onclick='javascript: gotoMonth(this, 8)'>Aug</a></div>
		<div class='first'><a href='#' onclick='javascript: gotoMonth(this, 9)'>Sept</a></div>
		<div><a href='#' onclick='javascript: gotoMonth(this, 10)'>Oct</a></div>
		<div><a href='#' onclick='javascript: gotoMonth(this, 11)'>Nov</a></div>
		<div><a href='#' onclick='javascript: gotoMonth(this, 12)'>Dec</a></div>
	</div>

	<script type="text/javascript" language="javascript">
	new Tip('monthPickerTarget', $('monthPickerDiv').cloneNode(true), {
		title: "Choose Month",
		target: $('monthPickerTarget'),
		hideOn: { element: 'closeButton', event: 'click' },
		stem: 'topRight',
		hook: { target: 'bottomMiddle', tip: 'topRight' },
		offset: { x: 6, y: 0 },
		width: 'auto',
		style: 'toolboxblue',
		showOn: 'click'
	});
	</script>

	<h3><?=$monthYearString?></h3>
	
	<div id='cContainer' class='clearfix'>
		<?php echo $this->renderElement('../scheduling/_days'); ?>
		
		<div id='pContainer'>
			<?php
			$row = 1;
			$this->data['masterRows'] = 0;
			foreach($packages as $package):
				echo $this->renderElement('../scheduling/_package_row', array('package' => $package, 'row' => $row++, 'masterRows' => $this->data['masterRows']));
			endforeach;
			?>
		</div>
	</div>
</div>
<?=$prototip->renderTooltips();?>