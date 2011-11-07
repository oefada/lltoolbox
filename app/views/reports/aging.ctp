<style>
	th.header {
		background-image: url(../img/tablesort_bg.gif);
		cursor: pointer;
		font-weight: bold;
		background-repeat: no-repeat;
		background-position: center left;
		padding: 0 10px 0 20px;
		border-right: 1px solid #dad9c7;
		margin-left: -1px;
		background-color: #f3e1b0;
		white-space: nowrap;
	}
	th.headerSortUp {
		background-image: url(/img/tablesort_asc.gif);
		background-color: #ecce7f;
	}
	th.headerSortDown {
		background-image: url(/img/tablesort_desc.gif);
		background-color: #ecce7f;
	}
	tr:nth-child(even) {
		background-color: #fafafa;
	}
</style>
<script>
	/*
	 * InPlaceEditor extension that adds a 'click to edit' text when the field is
	 * empty.
	 */
	Ajax.InPlaceEditorWithEmptyText = Class.create(Ajax.InPlaceEditor, {

		initialize : function($super, element, url, options) {

			if(!options.emptyText)
				options.emptyText = "click to edit&hellip";
			if(!options.emptyClassName)
				options.emptyClassName = "inplaceeditor-empty";

			$super(element, url, options);

			this.checkEmpty();
		},
		checkEmpty : function() {

			if(this.element.innerHTML.length == 0 && this.options.emptyText) {

				this.element.appendChild(new Element("span", {
					className : this.options.emptyClassName
				}).update(this.options.emptyText));
			}

		},
		getText : function($super) {

			if( empty_span = this.element.select("." + this.options.emptyClassName).first()) {
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
<div style="float:right;">
	<?=$html->link("View old aging report",array('action'=>'aging2'));?>
</div>
<div style="float:right;clear:both;">
	<?= $html->link('<span><b class="icon"></b>Export Report</span>' , array('action' => 'aging.csv') , array('class' => 'button excel') , null , false);?>
</div>
<table class="tablesorter tablefilter">
	<thead>
		<tr style="text-align:center;">
			<td colspan="2">Age</td>
			<td colspan="3">Client</td>
			<td colspan="1"></td>
			<td colspan="2"></td>
			<td colspan="2">Membership</td>
			<td colspan="2"># of Packages</td>
			<td colspan="2">Sites</td>
			<td colspan="2">Last Sell</td>
			<td colspan="2">Packages</td>
			<td></td>
		</tr>
		<tr>
			<th>Days</th>
			<th>30d</th>
			<th>Name</th>
			<th>Location</th>
			<th>Destination</th>
			<th>Manager</th>
			<th>Start</th>
			<th>End</th>
			<th>Fee</th>
			<th>Balance</th>
			<th>Total</th>
			<th>Remaining</th>
			<th>LL</th>
			<th>FG</th>
			<th>LL</th>
			<th>FG</th>
			<th>Date</th>
			<th>Price</th>
			<th>Notes</th>
		</tr>
	</thead>
	<tbody>
		<?php
foreach ($aging as $a):
		?>
		<tr>
			<td><?=$a['age'];?></td>
			<td><?=30*intval($a['age']/30);?></td>
			<td><?=$html->link($a['name'] , array('controller' => 'clients' , 'action' => 'edit' , 'id' => $a['clientId']));?></td>
			<td><?= $a['locationDisplay'];?></td>
			<td><?= $a['destinationName'];?></td>
			<td><?= $a['managerUsername'];?></td>
			<td><?=substr($a['startDate'] , 0 , 10);?></td>
			<td><?=substr($a['loaEndDate'] , 0 , 10);?></td>
			<td align="right">$<?=intval($a['membershipFee']);?></td>
			<td align="right">$<?=intval($a['membershipBalance']);?></td>
			
			<td><?=strpos($a['sites'] , 'luxurylink') === false ? '' : 'LL';?></td>
			<td><?=strpos($a['sites'] , 'family') === false ? '' : 'FG';?></td>

			<td><?=$a['offersLuxuryLink'] ? $a['offersLuxuryLink'] : '';?>
			<td><?=$a['offersFamily'] ? $a['offersFamily'] : '';?>

			<td><?=substr($a['lastSellDate'] , 0 , 10);?></td>
			<td align="right">$<?=intval($a['lastSellPrice']);?></td>
			<td><?=$a['membershipTotalPackages'];?></td>
			<td><?=$a['membershipPackagesRemaining'];?></td>
			<td>
			<p id="notes-<?=$a['loaId']?>">
				<?=$a['notes'];?>
			</p>
			<script type="text/javascript">
new Ajax.InPlaceEditorWithEmptyText("notes-<?=$a['loaId']?>", '/loas/inplace_notes_save', {rows:5,cols:30});</script></td>
			<?php
			echo "</tr>\n";
			endforeach;
			?>
	</tbody>
</table>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery.tablesorter.defaults.sortList = [[1,0],[5,0],[9,1]];
		jQuery("table.tablesorter").tablesorter();
	});

</script>
