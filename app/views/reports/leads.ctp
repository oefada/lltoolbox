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
<div style="float:right;">
	<?=$html->link('<span><b class="icon"></b>Export Report</span>', array('action' => 'leads.csv?format=excel' . ((isset($startDate) && !empty($startDate)) ? "&data[startDate][0]=$startDate" : '') . ((isset($endDate) && !empty($endDate)) ? "&data[startDate][1]=$endDate" : '') . ((isset($manager) && !empty($manager)) ? "&data[manager]=$manager" : '')), array('class' => 'button excel'), null, false);?>
</div>
<h2>Leads Report</h2>
<table class="tablesorter tablefilter">
	<thead>
		<tr>
			<th>Client</th>
			<th>Manager</th>
			<th>Lead Type</th>
			<th>Date</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email</th>
			<th>Address</th>
			<th>Phone</th>
			<th>Opt-in</th>
		</tr>
	</thead>
	<?php foreach ($results as $row):
	?>
	<tr>
		<td><?= $row['Client']['name'];?></td>
		<td><?= $row['Client']['managerUsername'];?></td>
		<td><?= $row['UserClientSpecialOffer']['leadType'];?></td>
		<td><?= substr($row['UserClientSpecialOffer']['created'], 0, 10);?></td>
		<td><?= $row['User']['firstName'];?></td>
		<td><?= $row['User']['lastName'];?></td>
		<td><?= $row['User']['email'];?></td>
		<?php
		$address = array();
		$rowAddress = reset($row['User']['Address']);
		$addresses = array();
		foreach (array('1','2','3') as $addressIndex) {
			if (isset($rowAddress['address' . $addressIndex])) {
				$addresses[] = $rowAddress['address' . $addressIndex];
			}
		}
		$address[] = implode(', ', $addresses);
		$addresses = array();
		foreach (array('city','stateCode','countryCode') as $addressIndex) {
			if (isset($rowAddress[$addressIndex])) {
				$addresses[] = $rowAddress[$addressIndex];
			}
		}
		$address[] = implode(', ', $addresses);
		$address = implode('<br/>', $address);
		?>
		<td><?= $address;?></td>
		<?php
		$phone = '';
		foreach (array('home','mobile','other','work') as $phoneType) {
			if (isset($row['User'][$phoneType . 'Phone']) && !empty($row['User'][$phoneType . 'Phone'])) {
				$phone = $row['User'][$phoneType . 'Phone'];
				break;
			}
		}
		?>
		<td><?= $phone;?></td>
		<td>Yes</td>
	</tr>
	<?php endforeach;?>
</table>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery.tablesorter.defaults.sortList = [[0, 0],[5,0],[4,0]];
		jQuery("table.tablesorter").tablesorter();
	});

</script>
