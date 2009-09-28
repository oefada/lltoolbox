<h2>Top 50 Clients</h2>
<table style="width: 400px; float: left">
	<thead>
	<tr>
		<th>Client</th>
		<th># of signups</th>
	</tr>
	</thead>
<tbody>
<?php foreach ($top as $k => $row): ?>
	<tr<?if($k % 2 == 0){ echo ' class="altrow"';}?>><td><?=$row['client']['name']?> <?=$row['dealAlert']['clientId']?></td><td><?=$row[0]['n']?></tr>
<?php endforeach;?>
</tbody>
</table>

<img src="http://chart.apis.google.com/chart?
chs=500x250
&chd=t:<?=implode(',',$points)?>
&cht=p
&chl=<?=implode('|',$clients)?>"
alt="Sample chart" />

<img src="http://chart.apis.google.com/chart?
chs=500x250
&chd=e:<?=$numSignups?>
&cht=lc
&chxt=x,y
&chxl=0:|<?=implode('|',$numSignupsWeek)?>"
alt="Sample chart" />