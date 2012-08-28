<?php $this->pageTitle = 'Unsub count per specific newsletter'; ?>

<h2><?php __('Unsubs per specific newsletter');?></h2>

<style>

.tdTitle{
	color:#FFFFFF;
	background-color:#000000;
	font-weight:bold;
}

</style>

<b>mailingListId of 0 means no mailingListId was found at the time of unsub-ing.</b><br><br>

<table cellpadding='4' cellspacing='0' border='1' width='500' style='width:500px;'>
<tr>
<td class='tdTitle'>mailingListId</td>
<td class='tdTitle'>siteId</td>
<td class='tdTitle'># unsubd</td>
</tr>
<?
//AppModel::printR($unsubMidArr);
foreach($unsubMidArr as $i=>$arr){

	echo "<tr>";
	echo "<td>".$arr['UnsubscribeLog']['mailingListInstanceId']."</td>";
	echo "<td>".$arr['UnsubscribeLog']['siteId']."</td>";
	echo "<td>".$arr[0]['num']."</td>";
	echo "</tr>";

}
echo "</table>";

