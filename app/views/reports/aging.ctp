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
	<?=$html->link("View old aging report" , array('action' => 'aging2'));?>
	<br/>
	<?=$html->link('<span><b class="icon"></b>Export Report</span>' , array('action' => 'aging.csv?format=excel'.((isset($startDate)&&!empty($startDate))?"&data[startDate][0]=$startDate":'').((isset($endDate)&&!empty($endDate))?"&data[startDate][1]=$endDate":'').((isset($manager)&&!empty($manager))?"&data[manager]=$manager":'')) , array('class' => 'button excel') , null , false);?>
</div>




<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create(array('action'=>'aging','type'=>'get'))?>
<fieldset>
<h3 class='title'>SEARCH <?=strtoupper($this->pageTitle)?> BY:</h3>
<div style="float: left; ">
<div class="fieldRow">
<label>Start Date</label>
<div class="range">
	<?=$datePicker->picker('startDate.0', array('label' => 'From','value'=>(isset($startDate)?substr($startDate,0,10):'')));?>
	<?=$datePicker->picker('startDate.1', array('label' => 'To','value'=>(isset($endDate)?substr($endDate,0,10):'')));?>
	<br/>
	<?php
		$dateranges = array(
							'Today'=>array(date('Y-m-d'),date('Y-m-d')),
							'Yesterday'=>array(  date('Y-m-d',strtotime('-1 day')),date('Y-m-d',strtotime('-1 day')) ),
							'This Week'=>array(  date('Y-m-d',strtotime('-7 day')),date('Y-m-d') ),
							'0-30 Days'=>array(  date('Y-m-d',strtotime('-30 day')),date('Y-m-d') ),
							'31-60 Days'=>array(  date('Y-m-d',strtotime('-60 day')),date('Y-m-d',strtotime('-31 day')) ),
							'61-90 Days'=>array(  date('Y-m-d',strtotime('-90 day')),date('Y-m-d',strtotime('-61 day')) ),
							'91-180 Days'=>array(  date('Y-m-d',strtotime('-180 day')),date('Y-m-d',strtotime('-91 day')) ),
							'181+ Days'=>array( '1970-01-01',date('Y-m-d',strtotime('-181 day')) ),
							
							'All Time'=>array('',''),
								) ;
								foreach ($dateranges as $k=>$v) {
	?>
	<a href="#" onclick='javascript: $("startDate0").value = "<?=$v[0]?>"; $("startDate1").value = "<?=$v[1];?>";return false;'><?=$k;?></a> |
	<?php } ?> 
</div>
</div>
<div class="fieldRow">
<label>Manager</label>
<div class="range">
	<select name="data[manager]">
		<option value=""<?=(empty($manager)?' selected':'')?>></option>
		<?php
		foreach ($managers as $m) :
		?>
		<option value="<?=$m;?>"<?=($m==$manager?' selected':'');?>><?=$m;?></option>
		<?php endforeach; ?>
	</select>	
</div>
</div>
<?=$form->submit('Search');?>
</div>
</fieldset>
<?=$form->end();?>
</div>






<table class="tablesorter tablefilter">
	<thead>
		<tr style="text-align:center;">
			<td colspan="1">Age</td>
			<td colspan="4">Client</td>
			<td colspan="4">Membership</td>
			<td colspan="2">Sites</td>
			<td colspan="2">Last Sell</td>
			<td colspan="1"></td>
			<td></td>
		</tr>
		<tr>
			<th>Days</th>
			<th>Name</th>
            <th>ClientID</th>
			<th>Location</th>
			<th>Destination</th>
			<th>Manager</th>
            <th>Account Exec</th>
			<th>Start</th>
			<th>End</th>
			<th>Fee</th>
			<th>Balance</th>
			<th>LL</th>
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
			<td><?=$html->link($a['name'] , array('controller' => 'clients' , 'action' => 'edit' , 'id' => $a['clientId']));?></td>
            <td><?= $a['clientId'];?></td>
			<td><?= $a['locationDisplay'];?></td>
			<td><?= $a['destinationName'];?></td>
			<td><?= $a['managerUsername'];?></td>
            <td><?= $a['accountExecutive'];?></td>
			<td><?=substr($a['startDate'] , 0 , 10);?></td>
			<td><?=substr($a['loaEndDate'] , 0 , 10);?></td>
			<td align="right"><?=($a['membershipTotalPackages']>0?$a['membershipTotalPackages'].' pkgs':'$'.intval($a['membershipFee']));?></td>
			<td align="right"><?=($a['membershipPackagesRemaining']>0?$a['membershipPackagesRemaining'].' pkgs':'$'.intval($a['membershipBalance']));?></td>
			
			<td><?=strpos($a['sites'] , 'luxurylink') === false ? '' : 'LL';?></td>
			<td><?=substr($a['lastSellDate'] , 0 , 10);?></td>
			<td align="right">$<?=intval($a['lastSellPrice']);?></td>
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
		jQuery.tablesorter.defaults.sortList = [[1, 0], [5, 0], [9, 1]];
		jQuery("table.tablesorter").tablesorter();
	});

</script>
