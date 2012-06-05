<div class="unsubscribeLogs form">
<h2><?php __('UnsubscribeLogs');?></h2>
<p>
<style>
#unsubcribe_logsAddForm select{
	margin-left:4px;
	margin-bottom:4px;
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

if (isset($unsubLogs) && count($unsubLogs)>0){

	?><br><hr><br><b><?
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
	echo "</table>";
}