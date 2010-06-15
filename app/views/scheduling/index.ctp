<?php $this->pageTitle = $clientName.$html2->c($clientId, 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']);
      $siteNames = array('1' => 'LL',
                         '2' => 'Family');
?>
<script>
function openSchedulingOverlay(date, packageId, packageName) {
	Modalbox.show("/scheduling_masters/add/packageId:"+packageId+"/date:"+date, {title: 'Scheduling <a href="/clients/<?=$clientId?>/packages/edit/'+packageId+'" target="_blank">'+packageName+'</a>'});
}
var dp = datePickerController.datePickers["dp-normal-1"];
function gotoMonth(theLink, month)
{
	$('spinner').show();
	yearSelect = $(theLink).up().previous('.yearPickerDiv').down('.yearPicker');
	var url = '/scheduling/index/clientId:<?=$clientId?>/month:'+month+'/year:'+$F(yearSelect);
	window.location.replace( url );
}

Event.observe(window, 'load', function() {
    $('selectSite').observe('change', function() {
            switch (this.value) {
                case 'LL':
                    $$('div.Family').each(function(divItem) {
                                $(divItem).hide();
                            });
                    $$('div.LL').each(function(divItem) {
                                $(divItem).show();
                            });
                    break;
                case 'Family':
                    $$('div.LL').each(function(divItem){
                                $(divItem).hide();
                            });
                    $$('div.Family').each(function(divItem) {
                                $(divItem).show();
                            });
                    break;
                case 'all':
                default:
                    ['LL', 'Family'].each(function(item) {
                        var elem = "div."+item;
                        $$(elem).each(function(divItem) {
                                $(divItem).show();
                        });
                    });
            }
    });
});

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

div.sitesDropdown {
    margin:10px 0 10px 0;
}

</style>
<h2 class='title'>Scheduling for <?=$monthYearString?></h2>
<?php if (count($currentLoa['Loa']['sites']) > 1): ?>
        <div class="sitesDropdown">
            <strong>Filter packages by site</strong>
            <select name="site" id="selectSite" />
                <option value="all" selected>All</option>
                <option value="LL">Luxury Link</option>
                <option value="Family">Family</option>
            </select>
        </div>
<?php endif; ?>
<div id='schedulingGraphs' style="float: left; clear: both">
	<?php echo $this->renderElement('../scheduling/_graph')?>
</div>
<div id='sContainer'>
	<div style="text-align: right" class='oKeys'><strong>KEY:</strong> <div class='oType1 oKey1'></div>Standard Auction <div class='oType2 oKey2'></div>Best Shot <div class='oType3 oKey3'></div>Exclusive <div class='oType4 oKey4'></div>Best Buy <div class='oType6 oKey6'></div>Dutch Auction<strong class='textRed'>Go To:</strong>
	<img src="/img/cal.png" id='monthPickerTarget' style='cursor: pointer' />
	<?php
	$prevYear = $nextYear = $year;
	$prevMonth = $nextMonth = $month;
	
	if ($month == 12) {
		$prevMonth = $month - 1;
		$nextMonth = 1;
		$nextYear = $year + 1;
	} elseif ($month == 1) {
		$prevMonth = 12;
		$prevYear = $year - 1;
	} else {
		$prevMonth = $month - 1;
		$nextMonth = $month + 1;
	}
	?>
	<br />
	<a href="/scheduling/index/clientId:<?=$clientId?>/month:<?=$prevMonth?>/year:<?=$prevYear?>">&lt; Prev Month</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/scheduling/index/clientId:<?=$clientId?>/month:<?=$nextMonth?>/year:<?=$nextYear?>">Next Month &gt;</a>
	<br /><br />
	</div>
	<div style="text-align:right;position:absolute;right:0px;">
		<?=$html->link('<span>Cancel ALL Offers</span>', "/scheduling/close_offers/clientId:{$client['Client']['clientId']}", array('onclick' => 'Modalbox.show(this.href, {title: this.title});return false','complete' => 'closeModalbox()','class' => 'button'), null, false)?>
		<br /><br />
	</div>
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
    	<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 1)'>Jan</a></div>
		<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 2)'>Feb</a></div>
		<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 3)'>Mar</a></div>
		<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 4)'>Apr</a></div>
		<div class='first'><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 5)'>May</a></div>
		<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 6)'>Jun</a></div>
		<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 7)'>Jul</a></div>
		<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 8)'>Aug</a></div>
		<div class='first'><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 9)'>Sept</a></div>
		<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 10)'>Oct</a></div>
		<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 11)'>Nov</a></div>
		<div><a href='javascript: void(0);' onclick='javascript: gotoMonth(this, 12)'>Dec</a></div>
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

	<h3 style="font-size: 13px;"><?=$monthYearString?></h3>

	<div id='cContainer' class='clearfix'>
		<?php echo $this->renderElement('../scheduling/_days'); ?>
		
		<div id='pContainer'>
			<?php
			$row = 1;
			$this->data['masterRows'] = 0;
			define('CELL_WIDTH',  100/$monthDays);
			foreach($packages as $package):
				echo $this->renderElement('../scheduling/_package_row', array('package' => $package, 'row' => $row++, 'masterRows' => $this->data['masterRows'], 'site' => $siteNames[$package['Package']['siteId']]));
			endforeach;
			?>
		</div>
	</div>
</div>
<?=$prototip->renderTooltips();?>
