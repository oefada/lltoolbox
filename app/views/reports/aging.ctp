<?// var_dump($this);exit;?><?php $this->pageTitle = "LOA Quarterly Aging Report" ?>
<style>
th {
	font-size: 11px;
	padding: 0;
}
</style>
<script>
/*
 * InPlaceEditor extension that adds a 'click to edit' text when the field is 
 * empty.
 */
Ajax.InPlaceEditorWithEmptyText = Class.create(Ajax.InPlaceEditor, {

  initialize : function($super, element, url, options) {

    if (!options.emptyText)        options.emptyText      = "click to edit&hellip";
    if (!options.emptyClassName)   options.emptyClassName = "inplaceeditor-empty";

    $super(element, url, options);

    this.checkEmpty();
  },

  checkEmpty : function() {

    if (this.element.innerHTML.length == 0 && this.options.emptyText) {

      this.element.appendChild(
          new Element("span", { className : this.options.emptyClassName }).update(this.options.emptyText)
        );
    }

  },

  getText : function($super) {

    if (empty_span = this.element.select("." + this.options.emptyClassName).first()) {
      empty_span.remove();
    }

    return $super();

  },

  wrapUp : function($super, transport) {
    this.checkEmpty();
    return $super(transport);
  }

});

</script>
<div class='index'>
<?php
/*
//TODO: put this in a helper
function $utilities->sortLink($field, $title, $view, $html) {

	//$url = "/reports/bids/filter:";
	$url = "/reports/aging";
	$url .= "/sortBy:$field";

	if (isset($view->params['named']['sortBy']) && $view->params['named']['sortBy'] == $field) {
		$dir = ($view->params['named']['sortDirection'] == 'ASC') ? 'DESC' : 'ASC';
	} elseif(isset($view->params['named']['sortBy'])  && $view->params['named']['sortBy'] == $field) {
		$dir = 'DESC';
	} else {
		$dir = 'ASC';
	}
	
	$url .= "/sortDirection:$dir";
	
	return $html->link($title, $url);

}
*/

if (!empty($results)): 
$i = 1;
$j = 1;

$grandTotalMembershipFee = 0;
$grandTotalMembershipBalance = 0;

?>
<?php foreach($results as $periodName => $timeperiod): ?>
	<a name="section-<?=$j?>"></a>
	<div style="float: right">
	<?= $html->link('<span><b class="icon"></b>Export Report</span>', array('action'=>'aging.csv'), array('class' => 'button excel'), null, false); ?>
	</div>
	<div><h2>Accounts aged <?=$periodName?> days</h2>
		<?php if ($showingOld) { ?>
			<a href="/reports/aging#section-<?=$j?>">Hide LOAs that expired more than 30 days ago</a>
		<?php } else { ?>
			<a href="/reports/aging?showOld=1#section-<?=$j?>">Show LOAs that expired more than 30 days ago</a>
		<?php }?><br />
	<?=count($timeperiod)?> records found</div>
	<table style="margin-top: 20px">
		<tr>
			<th>&nbsp;</th>
			<th><?=$utilities->sortLink('age', 'Age (Days)', $this, $html)?></th>
			<th><?="Destination";?></th>
			<th><?=$utilities->sortLink('Client.clientId', 'Client ID', $this, $html)?></th>
			<th><?=$utilities->sortLink('Client.name', 'Client Name', $this, $html)?></th>
			<th><?=$utilities->sortLink('Client.managerUsername', 'Account Manager', $this, $html)?></th>
			<th><?=$utilities->sortLink('Loa.startDate', 'Start Date', $this, $html)?></th>
			<th><?=$utilities->sortLink('loaEndDate', 'End Date', $this, $html)?></th>
			<th><?=$utilities->sortLink('membershipFee', 'Membership Fee', $this, $html)?></th>
			<th><?=$utilities->sortLink('membershipBalance', 'Remaining Balance', $this, $html)?></th>
			<th><?=$utilities->sortLink('lastSellPrice', 'Last Ticket Price', $this, $html)?></th>
			<th><?=$utilities->sortLink('lastSellDate', 'Last Ticket Date', $this, $html)?></th>
			<th><?=$utilities->sortLink('sites', 'Sites', $this, $html)?></th>
			<th>Packages Live</th>
			<th><?=$utilities->sortLink('Loa.notes', 'Notes', $this, $html)?></th>
		</tr>
<?php
$subtotalMembershipFee = 0;
$subtotalMembershipBalance = 0;
foreach ($timeperiod as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
$subtotalMembershipFee += (int)$r['Loa']['membershipFee'];
$subtotalMembershipBalance += (int)$r['Loa']['membershipBalance'];
?>
	<tr<?=$class?>>
		<td><?=$i++?></td>
		<td><?=$r[0]['age']?></td>
		<td><?=isset($r['Client']['destinationName'])?$r['Client']['destinationName']:'';?></td>
		<td><?=$r['Client']['clientId']?></td>
		<td><?=$r['Client']['name']?></td>
		<td><?=$r['Client']['managerUsername']?></td>
		<td><?=$r['Loa']['startDate']?></td>
		<td><?=$r[0]['loaEndDate']?></td>
		<td>
			<?php if ($r['Loa']['membershipBalance'] > 0) { echo $r['Loa']['membershipFee']; } ?>
			<?php if ($r['Loa']['membershipPackagesRemaining'] > 0) { echo "<br />{$r['Loa']['membershipTotalPackages']} packages<br />";} ?>
		</td>
		<td>
			<?php if ($r['Loa']['membershipBalance'] > 0) { echo $r['Loa']['membershipBalance']; } ?>
			<?php if ($r['Loa']['membershipPackagesRemaining'] > 0) { echo "<br />{$r['Loa']['membershipPackagesRemaining']} packages<br />";} ?>
		</td>
		<td><?=$r[0]['lastSellPrice']?></td>
		<td><?=$html->link($r[0]['lastSellDate'], '/loas/maintTracking/'.$r['Loa']['loaId'])?></td>

		<td><?=($r['Client']['sites'])?></td>
		<td> <?=isset($r['Client']['numOffers'])?$r['Client']['numOffers']:''?> </td>


		<td>
			<p id="notes-<?=$r['Loa']['loaId']?>"><?=$r['Loa']['notes']?></p>
			<script type="text/javascript">
			 new Ajax.InPlaceEditorWithEmptyText("notes-<?=$r['Loa']['loaId']?>", '/loas/inplace_notes_save', {rows:5,cols:30});
			</script>
		</td>
	</tr>
<?php endforeach; //TODO: add totals ?>
	<tr class="blackBg">
		<th colspan=8 style="text-align: right">Subtotals:</th>
		<th><?=$subtotalMembershipFee?></th>
		<th><?=$subtotalMembershipBalance?></th>
		<th colspan=6>
			&nbsp;
		</th>
	</tr>
	
<?php if($periodName != '0 to 30'): ?>
</table>
<?php endif;?>
<?php 
$j++;
$grandTotalMembershipFee += $subtotalMembershipFee;
$grandTotalMembershipBalance += $subtotalMembershipBalance;
endforeach; //end periods
?>
	<tr>
		<th colspan=13>&nbsp;</th>
	</tr>
	<tr class="blackBg">
		<th colspan=7 style="text-align: right">Grand Total:</th>
		<th><?=$grandTotalMembershipFee?></th>
		<th><?=$grandTotalMembershipBalance?></th>
		<th colspan=4>
			&nbsp;
		</th>
	</tr>
</table>
<?php else: ?>
	<div class='blankExample'>
		<h1>No results found</h1>
	</div>
<?php endif; ?>
</div>
