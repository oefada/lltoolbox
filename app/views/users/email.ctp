<?

echo "<b>$email</b>";
echo "<br><br>";
?>
<b>How it works.</b>
<br><br>
Emails that have userId's that all have matching rows in userSiteExtended OR no matching rows in userSiteExtended are processed first.  This makes userSiteExtended no longer a criteria for the rest of the script.
<br><br>Then set the primary userId by:<br><br>
1. user row with most recent modifyDateTime (login) that has a ticketId<br>
2. or the most recent (non null) modifyDateTime<br>
3. or the most recent userId<br><br>
<?
echo "<a href='?email=$email&process=1' onclick='return confirm(\"Are you sure?\");'><b>Process</b></a>";
echo "<br><br>";

if (count($rowArr)>0){

	echo "<table border=1 cellpadding=4 cellspacing=0 style='width:750px;'>";
	echo "<tr>";
	echo "<td>userId</td>";
	echo "<td>inactive</td>";
	echo "<td>createDateTime</td>";
	echo "<td>modifyDateTime</td>";
	echo "<td>ticketId</td>";
	echo "<td>userSiteExtendedId</td>";
	echo "</tr>";

	foreach($rowArr as $userId=>$row){

		echo "<tr>";
		echo "<td>";
		echo "<a href=/users/edit/".$row['userId'].">";
		echo $row['userId']."</span></a>";
		echo "</td>";
		echo "<td>".$row['inactive']."</td>";
		echo "<td>".$row['createDateTime']."</td>";
		echo "<td>".$row['modifyDateTime']."</td>";

		echo "<td>";
		echo isset($row['ticketId'])?$row['ticketId']:' - ';
		echo "</td>";

		echo "<td>";
		echo isset($row['userSiteExtendedId'])?$row['userSiteExtendedId']:' - ';
		echo "</td>";
		echo "</tr>";

	}	
	echo "</table>";

}else{
	echo "No rows found for $email";
}
