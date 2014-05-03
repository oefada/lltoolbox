<html><body>

<?=$mailing->displayHiddenClientIds($rows);?>

<table cellspacing="0" cellpadding="0" border="0" align="center" width="800" style="background-color: #ffffff;padding: 30px 0px 0px 0px;" valign="top">   
<? foreach($rows as $key=>$row){ 

	$arr=$row['client'];
	$offers=$row['client']['offers'];
	$seoUrl=$arr['seoUrl'];
	$seoUrl.="?showLeader=1&utm_medium=news&utm_source=inspiration";
	$seoUrl.="&utm_campaign=".$mailing->utmArr['utm_campaign'];
	$seoUrl.="&utm_content=properties";
	$seoUrl.="&utm_term=".$arr['seoName'];

?>


<tr> 
<td align="left" valign="top" style="padding: 32px 10px 0px 20px;">

	<table width="760" cellspacing="0" cellpadding="0" border="0" align="center">
	<tbody>
	<tr>
		<td 
			width="281" 
			valign="top" 
			height="148" 
			style="padding: 0px 20px 0px 0px;"
		><a 
			xt="SPCLICK" 
			target="_blank" 
			name="<?=$arr['seoName']?>" 
			href="<?=$seoUrl?>"
		><img 
			width="281" 
			height="148" 
			border="0" 
			style="display: block;" 
			alt="<?=$arr['name']?>" 
			name="<?=$arr['seoName']?>" 
			src="http://photos.luxurylink.us<?=$arr['imagePath']?>" 
		/></a>
		</td>
		<td valign="top">

			<table width="465"  cellspacing="0" cellpadding="0" border="0" valign="top">
			<tbody>
			<tr>
				<td 
					width="388" 
					valign="top" 
					align="left" 
					style="font-size:23px; line-height: 23px; font-family: Georgia1, Georgia, serif;color:#444444;padding: 0px 0px 0px 0px;"
				><a href="<?=$seoUrl?>" target="_blank" style=" text-decoration: none; color: #017fb8;"><?=$arr['name']?></a>
				</td> 
			</tr>

			<tr>
				<td 
					valign="top" 
					align="left" 
					style="font-size:13px; line-height: 14px; padding: 0px 0px 0px 0px;color:#666666;font-family: Arial,Sans-Serif;"><?=$arr['locationDisplay']?> 
				</td>
			</tr>

			<tr>
				<td 
					valign="top" 
					align="left" 
					style="font-size:13px; font-family: Arial,Sans-Serif;color:#666666;line-height:16px;padding: 15px 0px 0px 0px;"><?=$arr['longDesc']?> <a 
					href="<?=$seoUrl?>"  
					target="_blank" 
					style="text-decoration: none; color: #199bd7;"
				>More &#8594;</a> 
				</td>
			</tr>

			<tr>
				<td 
					valign="bottom"
					height="26" 
					align="right" 
					style="font-size:12px; font-family: Arial,Sans-Serif;color:#666666;line-height:16px;padding: 5px 30px 0px 0px;"><a 
					xt="SPCLICK" 
					target="_blank" 
					name="<?=$arr['seoName']?>" 
					href="<?=$seoUrl?>"
				><img 
					width="98" 
					height="26" 
					border="0" 
					alt="View Details" 
					name="<?=$arr['seoName']?>" 
					src="http://www.luxurylink.com/images/email/Inspiration/view-details.jpg" 
				/></a>
				</td>
			</tr>
			</tbody>
			</table>

		</td>
	</tr>
	</tbody>
	</table>

</td>
</tr>

<? } ?>
<img src="https://bounceexchange.com/tag/em/883.gif" width="1" height="1" border="0" />
</table>
</body></html>
