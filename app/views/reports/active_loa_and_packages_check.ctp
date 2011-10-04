<?php $this->pageTitle = "Active Loa and Packages Check" ?>
<style>
tr:nth-child(2n) {background-color:#dddddd}
</style>

<b><?=$site?></b><br><br>

<table>
<tr style='font-weight:bold;'>
<td>Client</td>
<td>loa Start Date</td>
<td>loa End Date</td>
<td>Account Manager</td>
<td>Destination</td>
<td>Num Packages Sold</td>
<td>Sales</td>
<td>loa Balance</td>
<td>Last Package End Date</td>
</tr>

<?

foreach($report_arr as $key=>$row){

	echo "<tr>";
	echo "<td>".$row['name']." : ".$row['clientId']."</td>";
	//echo "<td>".$row['site']."</td>";
	echo "<td>".date("Y-m-d",strtotime($row['loaStartDate']))."</td>";
	echo "<td>".date("Y-m-d",strtotime($row['loaEndDate']))."</td>";
	echo "<td>".$row['accountManager']."</td>";
	echo "<td>".$row['destination']."</td>";
	echo "<td>".number_format($row['numPackagesSold'])."</td>";
	echo "<td>".number_format($row['sales'])."</td>";
	echo "<td>".number_format($row['loaBalance'])."</td>";
	echo "<td>";
	echo is_numeric($row['lastPackageEndDate'])?date("Y-m-d",strtotime($row['lastPackageEndDate'])):$row['lastPackageEndDate'];
	echo "</td>";
	echo "</tr>";
	
}

?>

</table>
