<div class="unsubscribeLogs form">
<h2><?php __('UnsubscribeLogs');?></h2>
<p>
<style>
#unsubcribe_logsAddForm select{
	margin-left:4px;
	margin-bottom:4px;
}
.tdTitle{
	color:#FFFFFF;
	background-color:#000000;
	font-weight:bold;
}
input radio{width:50px;
form div{padding:0px;}
</style>
<script>

jQuery(document).ready(function($){
	$("#unsubForm").submit(function(){
		var output=$("#unsubForm input[type='radio']:checked").val();
		if (output=='csv'){
			$("#unsubForm").attr('action','/unsubscribe_logs/export/unsub_export_'+<?=date("Ymd")?>+'.csv');
		}else{
			$("#unsubForm").attr('action','/unsubscribe_logs/index');
		}
	});
});
</script>

<?

echo "<table style='border:0px;' border=0 cellpadding=4 cellspacing=0>";
echo "<tr>";
echo "<td valign='top' width=300>";

echo $form->create('unsubscribe_logs',array('id'=>'unsubForm','action'=>'index','type'=>'post')); 
echo "<b>Retrieve all unsubscribers that unsubscribed between:</b><br><br>  ";
echo $form->year('start', (date("Y")-5), (date("Y")+1), $startYear);
echo $form->month('start', $startMonth);
echo $form->day('start', $startDay);
echo "<br><br>and<br><br>";
echo $form->year('end', (date("Y")-5), (date("Y")+1), $endYear);
echo $form->month('end', $endMonth);
echo $form->day('end', $endDay);

echo "<br><br>for<br><br><b>Mailing List</b>";
echo $form->input('mailingList', array('label'=>false, 'options'=>$nlIdArr));

$options=array( 'csv'=>'csv', 'html'=>'html');
$attributes=array('legend'=>false, 'value'=>$output, 'separator'=>' &nbsp; ');
echo "<br><b>Output</b><br><br>";
echo $form->radio('Output', $options, $attributes);

echo $form->end(array('name'=>'Retrieve','label'=>'Retrieve'));

echo "</td><td valign=top width=500>";

if (isset($unsubLogs) && count($unsubLogs)>0){

	?><b><?
	echo $nl." ";
	echo date("Y-m-d",$start_ut).' to '.date("Y-m-d",$end_ut)."<br>";
	?></b>
	<br>

	<table cellpadding='4' cellspacing='0' border='1' width='600' style='width:600px;'>
	<tr>
	<td> &nbsp; </td>
	<td>Email</td>
	<td>subDate</td>
	<td>unsubDate</td>
	</tr>
	<?
	foreach($unsubLogs as $key=>$row){
		echo '<tr>';
		echo '<td>'.($key+1).'</td>';
		echo '<td>'.$row['UnsubscribeLog']['email'].'</td>';
		echo '<td>'.$row[0]['subDateYmd'].'</td>';
		echo '<td>'.$row[0]['unsubDateYmd'].'</td>';
		echo '</tr>';
	}
	echo "</table><br>";

}

?>

<b>Users Sub'd per Our Dbs</b><br>
<table cellpadding='4' cellspacing='0' border='1' style='width:500px;' width='500'>
<tr>
<td class='tdTitle'><b>mailingListId</b></td>
<td class='tdTitle'>newsletter</td>
<td class='tdTitle'># Sub'd</td>
</tr>

<?

foreach($subCountArr as $i=>$row){
	$nlId=$row['userMailOptin']['mailingListId'];
	echo "<tr>";
	echo "<td>".$nlId."</td>";
	echo "<td>".$nlIdArr[$nlId]."</td>";
	echo "<td>".number_format($row[0]['num'])."</td>";
	echo "</tr>";
}
echo "</table>";

echo "</td><td valign=top>";

	echo "<b>Number of unsubs by mailingId and month</b>";

	echo "<table border=1 cellpadding=4 cellspacing=0 width='300' style='width:300px;'>";
	echo "<tr><td class='tdTitle'>mailingId</td><td class='tdTitle'># unsubs</td><td class='tdTitle'>Year-Month</td></tr>";
	$total=0;
	foreach($unsubCountArr as $key=>$rows){
		foreach($rows as $j=>$arr){
			$nlId=$arr['unsubscribeLog']['mailingId'];
			$siteId=$arr['unsubscribeLog']['siteId'];
			if (!isset($p[$siteId][$nlId])){
				echo "<tr><td colspan=3 bgcolor=#cccccc>";
				echo $nlDataArr[$siteId][$nlId]['name']." : ".$nlId;
				echo "</td></tr>";
				$p[$siteId][$nlId]=1;
			}
			echo "<tr><td>".$siteId."</td>";
			echo "<td>".number_format($arr[0]['num'])."</td>";
			echo "<td>".$arr[0]['unsubDate_ym']."</td>";
			echo "</tr>";
			$total+=$arr[0]['num'];
		}
	}
	echo "<tr><td align=right> Total:</td><td>".number_format($total)."</td><td> &nbsp; </td></tr>";
	echo "</table>";

echo "</td></tr></table>";


