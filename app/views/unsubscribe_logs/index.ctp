<div class="unsubscribeLogs form">
<h2><?php __('UnsubscribeLogs');?></h2>
<p>
<style>
#unsubcribe_logsAddForm select{
	margin-left:4px;
	margin-bottom:4px;
}
form div{padding:0px;}
</style>

<?

echo $form->create('unsubscribe_logs',array('url'=>'/unsubscribe_logs/export/unsub_export_'.date("Ymd").'.csv')); 
echo "<b>Retrieve all unsubscribers that unsubscribed between:</b><br><br>  ";
//echo "<b>Date</b><br>";
echo $form->year('start', (date("Y")-5), (date("Y")+1), $startYear);
echo $form->month('start', $startMonth);
echo $form->day('start', $startDay);
echo "<br><br>and<br><br>";
//echo "<b>End Date</b><br>";
echo $form->year('end', (date("Y")-5), (date("Y")+1), $endYear);
echo $form->month('end', $endMonth);
echo $form->day('end', $endDay);

echo "<br><br>for<br><br><b>Mailing List</b>";
echo $form->input('mailingList', array('label'=>false, 'options'=>$nlIdArr));


echo $form->end(array('name'=>'Retrieve','label'=>'Retrieve'));



