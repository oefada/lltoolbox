<?php

$result = mysql_query("SELECT s.mailing_schedule_id, s.mailing_type_id, s.mailing_timestamp, s.mailing_subject, n.mailing_name, mailing_type.mailing_type_description 
	FROM mailing_schedule s INNER JOIN mailing_type ON s.mailing_type_id = mailing_type.mailing_type_id 
	LEFT OUTER JOIN mailing_name n ON s.mailing_schedule_id = n.mailing_schedule_id 
	ORDER BY s.mailing_timestamp DESC LIMIT 50");

while($row = mysql_fetch_array($result)) {
	$mailing_schedules[$row['mailing_schedule_id']] = $row;
}

foreach($mailing_schedules as $mailing_schedule_id=>$mailing_schedule) {
	$query = "SELECT l.loaId, s.mailing_schedule_id, l.clientId, client.name as client_name
				FROM mailing_segment s
				INNER JOIN mailing_segment_position sp ON s.mailing_segment_position_id = sp.mailing_segment_position_id
				LEFT OUTER JOIN mailing_segment_credit c ON s.mailing_segment_id = c.mailing_segment_id
				LEFT OUTER JOIN loa l ON c.loa_id = l.loaId LEFT JOIN client ON l.clientId = client.clientId
				WHERE     (mailing_schedule_id = $mailing_schedule_id)
				ORDER BY  sp.mailing_segment_position_sort_order, s.mailing_segment_sort_order";


	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) {
		//$products[$row['mailing_schedule_id']][] = $row;
		//$mailing_schedules[$row['mailing_schedule_id']]['products'][$row['clientId']] = $row;
		$clients[$mailing_schedule_id][] = $row;
	}	
}
//print_r($clients);
?>
<p>
	Blast listing
	<br><a style="font-size: 12px; font-weight: normal;" href="ns_main?pg=ns_maint">[+] Add new</a>
</p>

<table border="1" cellspacing="0" cellpadding="4">
	<tr>
		<td><b>Theme/Description</b></td>
		<td width="200"><b>Subject</b></td>
		<td align="center"><b>Blast date</b></td>
		<td><b>Product(s)</b></td>
		<td>&nbsp;</td>
	</tr>
<?

foreach($mailing_schedules as $mailing_schedule) { ?>
	
	<tr>
		<td width="300" valign="top"><a href="ns_main?pg=ns_maint&mailing_schedule_id=<?=$mailing_schedule['mailing_schedule_id']?>"><?=($mailing_schedule['mailing_name']) ? $mailing_schedule['mailing_name'] : date('D, m-d-Y h:ia', $mailing_schedule['mailing_timestamp']) . ' (imported)'?></a></td>
		<td><?=$mailing_schedule['mailing_subject']?></td>
		<td valign="top"><?=date('D, m-d-Y h:ia', $mailing_schedule['mailing_timestamp'])?></td>
		<td><?=display_segment_client_data($clients[$mailing_schedule['mailing_schedule_id']])?></td>
		<td><a href="ns_view_html?mailing_schedule_id=<?=$mailing_schedule['mailing_schedule_id']?>&preview_html=1" target="_blank">Preview HTML</a></td>
	</tr>
<? } ?>
</table>

</p>