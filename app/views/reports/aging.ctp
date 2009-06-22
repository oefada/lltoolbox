<?php $this->pageTitle = "LOA Quarterly Aging Report" ?>
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
//TODO: put this in a helper
function sortLink($field, $title, $view, $html) {
	$url = "/reports/bids/filter:";
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

if (!empty($results)): 
$i = 1;
$j = 1;
?>
<?php foreach($results as $periodName => $timeperiod): ?>
	<a name="section-<?=$j?>"></a>
	<div style="float: right">
	<?= $html->link('<span><b class="icon"></b>Export Report</span>', array('action'=>'aging.csv'), array('class' => 'button excel'), null, false); ?>
	</div>
	<div><h2>Accounts aged <?=$periodName?> days</h2><?=count($timeperiod)?> records found</div>
	<table style="margin-top: 20px">
		<tr>
			<th>&nbsp;</th>
			<th><?=sortLink('Offer.offerId', 'Age (Days)', $this, $html)?></th>
			<th><?=sortLink('Offer.offerId', 'Client ID', $this, $html)?></th>
			<th><?=sortLink('Client.name', 'Client Name', $this, $html)?></th>
			<th><?=sortLink('Track.applyToMembershipBal', 'LOA ID', $this, $html)?></th>
			<th><?=sortLink('Offer.offerTypeName', 'Start Date', $this, $html)?></th>
			<th><?=sortLink('country', 'End Date', $this, $html)?></th>
			<th><?=sortLink('state', 'Membership Fee', $this, $html)?></th>
			<th><?=sortLink('city', 'Remaining Balance', $this, $html)?></th>
			<th><?=sortLink('Loa.notes', 'Notes', $this, $html)?></th>
		</tr>
<?php foreach ($timeperiod as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$i++?></td>
		<td><?=$r[0]['age']?></td>
		<td><?=$r['Client']['clientId']?></td>
		<td><?=$r['Client']['name']?></td>
		<td><?=$r['Loa']['loaId']?></td>
		<td><?=$r['Loa']['startDate']?></td>
		<td><?=$r[0]['loaEndDate']?></td>
		<td><?=$r['Loa']['membershipFee']?></td>
		<td><?=$r['Loa']['membershipBalance']?></td>
		<td>
			<p id="notes-<?=$r['Loa']['loaId']?>"><?=$r['Loa']['notes']?></p>
			<script type="text/javascript">
			 new Ajax.InPlaceEditorWithEmptyText("notes-<?=$r['Loa']['loaId']?>", '/loas/inplace_notes_save', {rows:5,cols:30});
			</script>
		</td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?php 
$j++;
endforeach; //end periods?>

<?php else: ?>
	<div class='blankExample'>
		<h1>No results found</h1>
	</div>
<?php endif; ?>
</div>