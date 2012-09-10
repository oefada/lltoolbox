<?

echo "<b>$email</b>";
echo "<br><br>green=keep, yellow=make inactive, red=delete - ";
echo "<a href='?email=$email&process=1' onclick='return confirm(\"Are you sure?\");'><b>Do it</b></a>";
echo "<br><br>";

echo "<table border=1 cellpadding=4 cellspacing=0 style='width:750px;'>";
echo "<tr>";
echo "<td>userId</td>";
echo "<td>inactive</td>";
echo "<td>createDateTime</td>";
echo "<td>modifyDateTime</td>";
echo "<td>ticketId</td>";
echo "<td>userSiteExtendedId</td>";
echo "</tr>";

krsort($rowArr);
foreach($rowArr as $row){

	echo "<tr>";
	echo "<td>";
	echo "<a href=/users/edit/".$row['userId'].">";
	echo "<span style='background-color:";
	if (in_array($row['userId'], $keepArr[$row['email']])){
		echo "green;color:#ffffff;";
	}else if (in_array($row['userId'],$delIdArr)){
		echo "#e80000;color:#ffffff;";
	}else{
		echo "white";
	}
	echo ";'>";
	echo $row['userId']."</span></a>";
	echo "</td>";
	echo "<td>".$row['inactive']."</td>";
	echo "<td>".$row['createDateTime']."</td>";
	echo "<td>".$row['modifyDateTime']."</td>";
	echo "<td>";
	echo "<span style='background-color:";
	echo in_array($row['userId'], $inactiveArr[$row['email']])?"yellow":"white";
	echo ";'>";
	echo $row['ticketId']."</span></td>";
	echo "<td>".$row['userSiteExtendedId']."</td>";
	echo "</tr>";

}	
echo "</table>";
