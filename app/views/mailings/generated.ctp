<?

Configure::write('debug', '0');

// These functions wrap up the html
function displayEditorsPicksImg($arr,$date_ymd,$version){ 

	$name=$arr['client']['name'];
	$locationDisplay=$arr['client']['locationDisplay'];
	$seoLink=$arr['client']['seoUrl'];
	$seoName=$arr['client']['seoName'];
	$img="http://www.luxurylink.com".$arr['client']['imagePath'];//http://www.luxurylink.com/images/por/0-8358/0-8358-gal-xl-08.jpg

?>

<a xt="SPCLICK" name="www_luxurylink_com_fivest_17" target="_blank" href="<?=$seoLink?>?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=<?=$seoName?>"><img name="Cont_32" width="325" height="182" style="display:block" alt="<?=$name?>" border="0" src="<?=$img?>" /></a>

<? }

function displayEditorsPicksText($arr,$date_ymd,$version){ 

	$name=$arr['client']['name'];
	$locationDisplay=$arr['client']['locationDisplay'];
	$shortBlurb=($arr['client']['longDesc']);
	$seoLink=$arr['client']['seoUrl'];
	$seoName=$arr['client']['seoName'];
	$img="http://www.luxurylink.com".$arr['client']['imagePath'];
	//http://www.luxurylink.com/images/por/0-8358/0-8358-gal-xl-08.jpg

?> 

<strong style="font-size:13px"><?=$locationDisplay?><br />
<a xt="SPCLICK" name="www_luxurylink_com_fivest_18" style="color:#33669a" target="_blank" href="<?=$seoLink?>?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=<?=$seoName?>"><?=$name?></a></strong><br />
<br />
<?=$shortBlurb?>
<br />
<br />
<br />

<a xt="SPCLICK" name="www_luxurylink_com_fivest_19" style="color:#33669a" target="_blank" href="<?=$seoLink?>?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=<?=$seoName?>"><img name="Cont_8" align="right" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/btn-viewpackage.gif" /></a>

<?

}

function displayBox2($arr,$date_ymd,$version){ 

	$name=$arr['client']['name'];
	$locationDisplay=$arr['client']['locationDisplay'];
	$shortBlurb=$arr['client']['longDesc'];
	$seoLink=$arr['client']['seoUrl'];
	$seoName='';
	$seoName=$arr['client']['seoName'];
	$img="http://www.luxurylink.com".$arr['client']['imagePath'];//http://www.luxurylink.com/images/por/0-8358/0-8358-gal-xl-08.jpg


?>

<p style="background:#86878a;border-bottom:2px solid #86878a;padding:0;vertical-align:middle;color:#ffffff;height:16px;margin:0 0 2px 0;font-size:12px;line-height:18px">&nbsp;<?=$locationDisplay?></p>
<a xt="SPCLICK" name="www_luxurylink_com_fivest_6" target="_blank" href="<?=$seoLink?>?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=<?=$seoName?>"><img name="Cont_26" style="display:block;margin-bottom:5px" alt="<?=$name?>" width="325" height="175" border="0" src="<?=$img?>" /></a>     <a xt="SPCLICK" name="www_luxurylink_com_fivest_7" style="color:#33669a;font-size:12px;line-height:16px" target="_blank" href="<?=$seoLink?>?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=<?=$seoName?>"><?=$name?></a><br />
<?=$shortBlurb;?>
<br />
<a xt="SPCLICK" name="www_luxurylink_com_fivest_8" target="_blank" href="<?=$seoLink?>?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=<?=$seoName?>"><img name="Cont_8" align="right" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/btn-viewpackage.gif" /></a>

<?

}

function displayBox($arr,$date_ymd,$version){

	$name=$arr['client']['name'];
	$locationDisplay=$arr['client']['locationDisplay'];
	$shortBlurb=$arr['client']['longDesc'];
	$seoLink=$arr['client']['seoUrl'];
	$seoName=$arr['client']['seoName'];
	$img="http://www.luxurylink.com".$arr['client']['imagePath'];//http://www.luxurylink.com/images/por/0-8358/0-8358-gal-xl-08.jpg

?>

<p style="background:#86878a;border-bottom:2px solid #86878a;padding:0;vertical-align:middle;color:#ffffff;height:16px;margin:0 0 2px 0;font-size:12px;line-height:18px">&nbsp;<?=$locationDisplay?></p>
<a name="0.1_www_luxurylink_com_fi_2" target="_blank" xt="SPCLICK" href="<?=$seoLink?>?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=<?=$seoName?>"><img name="Cont_9" alt="<?=$name?>" width="239" height="148" border="0" style="display:block;margin-bottom:5px" src="<?=$img?>" /></a>     <a xt="SPCLICK" name="0.1_www_luxurylink_com_fi_2" style="color:#33669a;font-size:12px;line-height:16px" target="_blank" href="<?=$seoLink?>?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=<?=$seoName?>"><?=$name?></a><br />
<?=$shortBlurb;?>
<br />
<a name="0.1_www_luxurylink_com_fi_3" target="_blank" xt="SPCLICK" href="<?=$seoLink?>?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=<?=$seoName?>"><img name="Cont_8" align="right" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/btn-viewpackage.gif" /></a>

<? } ?>

<div style="margin:0;padding:0">
<table width="100%" bgcolor="#f6f6f6" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td>
<table width="730" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif;font-size:11px;line-height:15px;text-align:center;color:#86878a">
<tbody>
<tr>
<td height="40">If you are unable to see the message below, <a xt="SPCLICK" name="www_luxurylink_com_images_emai" style="color:#0066ff" target="_blank" href="http://www.luxurylink.com/images/email/<?=$date_ymd?>_b.html">click here to view.</a><br />

To ensure delivery to your inbox, please add <a xt="SPEMAIL" name="insider_e_luxurylink_mail_com" style="color:#0066ff" target="_blank" href="mailto:insider@luxurylink.com">insider@luxurylink.com</a> to your address book.</td>
</tr>
<tr>
<td><img name="Cont_0" width="730" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-730.gif" /></td>
</tr>
<tr>
<td height="10"><img name="Cont_1" height="10" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>

</tr>
</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0" style="color:#666666">
<tbody>
<tr>
<td width="20" rowspan="3"></td>
<td width="465"><a name="0.1_logo" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=logo"><img name="Cont_2" alt="Luxury Link" border="0" src="http://www.luxurylink.com/images/insider/images1/logo-luxurylink.gif" /><img name="Cont_3" alt="INSIDER" border="0" src="http://www.luxurylink.com/images/insider/images1/txt-insider.gif" /></a></td>
<td width="225" valign="top" style="font-family:Verdana, Arial, sans-serif;font-size:11px;color:#333333;text-align:right">

<table cellpadding="0" cellspacing="0" border="0" width="207" align="right">
<tbody>
<tr>
<td valign="bottom" align="left" style="padding:0px 10px 0px 0px" width="42"><a name="Forward" xt="SPFORWARD" target="_blank" href="#SPFORWARD"><img border="0" name="0.1_share" alt="Send to a Friend" width="129" height="26" src="http://www.luxurylink.com/images/insider/send-friend.gif" /></a></td>
<td valign="bottom" align="left" style="padding:0px 0px 0px 0px" width="42"><img border="0" name="0.1_share" alt="Share" width="40" height="23" src="http://www.luxurylink.com/images/insider/share.gif" /></td>
<td valign="middle" align="left" style="padding:0px 0px 0px 0px"><a xt="SPSNCLICK" xtsn="FB" name="share-facebook" target="_blank" href="#SPSNCLICK"><img border="0" name="0.1_share-facebook" width="19" height="19" alt="" src="http://www.luxurylink.com/images/insider/fb.gif" /></a></td>
<td valign="middle" align="left" style="padding:0px 0px 0px 0px"><a xt="SPSNCLICK" xtsn="TW" name="share-twitter" target="_blank" href="#SPSNCLICK"><img border="0" name="0.1_share-twitter" width="19" height="19" alt="" src="http://www.luxurylink.com/images/insider/t.gif" /></a></td>
</tr>
</tbody>

</table>
<br />
<br />
<span style="font-size:14px;color:#666666"><?=date("M d, Y")?></span></td>
<td width="20" rowspan="3"></td>
</tr>
<tr>
<td height="5" colspan="2"><img name="Cont_1" height="5" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>

</tr>
<tr>
<td width="465" valign="top" style="font-family:Verdana, Arial, sans-serif;font-size:13px;line-height:16px">
<h2 style="font-family:Georgia, Times New Roman, serif;font-size:24px;margin:0;font-weight:normal;color:#666666;line-height:35px">Travel Experiences You Dream Of</h2>
You come to Luxury Link to find the travel experiences you dream of: <i>the world's finest hotels, at unthinkable prices</i>. We are passionate about continuing to discover new and incredible experiences that put the next 'check' on your bucket list. <a name="0.1_www_luxurylink_com_80_1" style="color:#336699" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/fivestar/hotel-deals/staff_picks?kw=staff_picks&pp=10&did=0&guest=0&len=0&offerAuc=1&offerFp=1&x=62&y=9&sortb=bestselling&pg=1&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=text">Explore what Luxury Link has to offer</a>.</td>
<td width="225" valign="top" style="font-family:Verdana, Arial, sans-serif;font-size:12px;color:#336699;line-height:24px;padding-left:10px">

<div style="border-bottom:1px solid #cccccc;font-size:15px;color:#336699">In this Issue</div>
<div style="margin:7px 0px 7px 18px;text-transform:uppercase;line-height:24px">&bull;  <a name="0.1__section_NEW" style="text-decoration:underline;color:#336699" xt="SPBOOKMARK" href="#0.1_section_NEW">NEW OFFERS</a><br />
&bull;  <a name="0.1__section_TOP" style="text-decoration:underline;color:#336699" xt="SPBOOKMARK" href="#0.1_section_TOP">TOP DEALS</a><br />
&bull;  <a name="0.1__section_EDITORS" style="text-decoration:underline;color:#336699" xt="SPBOOKMARK" href="#0.1_section_EDITORS">EDITORS PICKS</a><br />
&bull;  <a name="0.1__section_MYSTERY" style="text-decoration:underline;color:#336699" xt="SPBOOKMARK" href="#0.1_section_MYSTERY">Mystery Auction</a></div>

</td>
</tr>
<tr>
<td height="20" colspan="4"><img name="Cont_1" height="20" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>
</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0" style="color:#666666;font-family:Arial, Verdana, sans-serif;font-size:11px;text-transform:uppercase;text-align:center">
<tbody>

<tr>
<td colspan="15" height="10" valign="middle"><img name="Cont_0" width="730" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-730.gif" /></td>
</tr>
<tr>
<td width="10"></td>
<td valign="middle" width="90"><a name="0.1_www_luxurylink_com_fivestar_ut" style="color:#666666;text-decoration:none" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/fivestar?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=destinations">DESTINATIONS</a></td>
<td valign="middle" width="12">|</td>
<td valign="middle" width="114"><a name="0.1_www_luxurylink_com_last_m_1" style="color:#666666;text-decoration:none" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/last-minute-travel-deals/limited-time?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=last-minute-deal">LAST MINUTE DEALS</a></td>

<td valign="middle" width="12">|</td>
<td valign="middle" width="90"><a name="0.1_www_luxurylink_com_fivestar_al" style="color:#666666;text-decoration:none" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/fivestar/all-inclusive-resorts/deals?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=all-inclusive">ALL-INCLUSIVE</a></td>
<td valign="middle" width="12">|</td>
<td valign="middle" width="75"><a name="0.1_www_luxurylink_com_last_minute" style="color:#666666;text-decoration:none" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/fivestar/estates-villas/deals?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=villas">VILLAS</a></td>
<td valign="middle" width="12">|</td>
<td valign="middle" width="60"><a name="0.1_www_luxurylink_com_la_1" style="color:#666666;text-decoration:none" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/fivestar/europe/hotels?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=europe">EUROPE</a></td>

<td valign="middle" width="12">|</td>
<td valign="middle" width="85"><a name="0.1_www_luxurylink_com_fivest_1" style="color:#666666;text-decoration:none" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/fivestar/travel-deals/california?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=california">CALIFORNIA</a></td>
<td valign="middle" width="12">|</td>
<td valign="middle" width="85"><a name="0.1_www_luxurylink_com_fi_1" style="color:#666666;text-decoration:none" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/fivestar/caribbean-and-bermuda/hotels?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=caribbean">CARIBBEAN</a></td>
<td width="10"></td>
</tr>
<tr>

<td colspan="15" height="10" valign="middle"><img name="Cont_0" width="730" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-730.gif" /></td>
</tr>
<tr>
<td height="10" colspan="15"><img name="Cont_1" height="10" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>
</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0">
<tbody>

<tr>
<td>
<table width="551" align="left" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif;font-size:13px;background-color: #ffffff;">
<tbody>
<tr>
<td><img name="Cont_4" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/title-newoffers-top.gif" /></td>
</tr>
<tr>
<td style="border-bottom:1px solid #9c9da0;border-left:1px solid #9c9da0;border-right:1px solid #9c9da0;color:#333333">

<table border="0" width="100%" valign="top" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td valign="top"><a name="0.1_section_NEW" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/last-minute-travel-deals/new-offer?offerAuc=1&offerFp=1&sortb=bestselling&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=new">     <img name="Cont_5" alt="New Offers" border="0" src="http://www.luxurylink.com/images/insider/images1/title-newoffers.gif" /></a></td>
<td align="right" valign="bottom" style="font-family:Arial, Verdana, sans-serif;font-size:13px;background-color: #ffffff;padding: 0px 20px 0px 0px;"><a xt="SPCLICK" name="0.1_section_NEW" style="color:#33669a;" target="_blank" href="http://www.luxurylink.com/last-minute-travel-deals/new-offer?offerAuc=1&offerFp=1&sortb=bestselling&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=new">View All New Offers</a></td>
</tr>
</tbody>
</table>

<table align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif">
<tbody>
<tr>
<td width="20" rowspan="6"></td>
<td height="20" colspan="3"><img name="Cont_6" width="511" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-510.gif" /></td>
<td width="20" rowspan="6"></td>
</tr>
<tr>


<td width="239" style="font-size:11px" valign="top">

<?  displayBox($rows[0],$date_ymd,$version); ?>

</td>
<td width="33"></td>

<td width="239" style="font-size:11px" valign="top">

<? displayBox($rows[1],$date_ymd,$version); ?>

</td>
</tr>

<tr>
<td height="30" colspan="3"><img name="Cont_6" width="511" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-510.gif" /></td>
</tr>
<tr>
<td width="239" style="font-size:11px" valign="top">

<? displayBox($rows[2],$date_ymd,$version); ?>

</td>
<td width="33"></td>
<td width="239" style="font-size:11px" valign="top">

<? displayBox($rows[3],$date_ymd,$version); ?>

</td>
</tr>
<tr>
<td height="20" colspan="3"></td>
</tr>
</tbody>
</table>

</td>
</tr>
</tbody>
</table>
<table width="162" align="right" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif;color:#444444;line-height:16px">
<tbody>
<tr>
<td><img name="Cont_1" height="9" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>

<tr>
<td align="center" style="padding:10px 15px;text-align:left;border:1px solid #9c9da0;font-size:11px"><a xt="SPCLICK" name="www_luxurylink_com_my_my_newsl" target="_blank" href="https://www.luxurylink.com/my/my_newsletters.php?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=myaccount">     <img name="Cont_13" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/ads/insertions/myaccount.jpg" /></a>     To edit or change your account settings including email preferences please go to &quot;My Account&quot;</td>
</tr>
<tr>
<td><img name="Cont_1" height="15" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>
<tr>

<td align="center" style="padding:10px 12px 10px 13px;text-align:left;border:1px solid #9c9da0;font-size:12px"><a xt="SPCLICK" name="0.1_logo" target="_blank" href="http://www.luxurylink.com/?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=logo">     <img name="Cont_14" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/logo-small-luxurylink.gif" /></a>     We pride ourselves on hand-selecting the world's finest luxury     hotels &amp; resorts, offering them to you at an incredible value.      <img width="135" height="1" name="Cont_15" style="display:block;margin-top:10px" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/hr-135.gif" /><br />
<strong style="line-height:14px">Have a question?<br />
We'd love to help!</strong><br />
Our expert concierges 	are all experienced 	travel agents, and 	would love to assist 	you in planning your next luxury vacation. <br />
<br />

Call us any time <br />
between 7am-5pm<br />
Pacific time at<br />
<strong>1-888-297-3299</strong></td>
</tr>
</tbody>
</table>

</td>
</tr>
</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td><img name="Cont_1" height="15" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>

</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif;font-size:13px;background-color: #ffffff;">
<tbody>
<tr>
<td><a xt="SPCLICK" name="www_luxurylink_com_fivestar_tr" target="_blank" href="http://www.luxurylink.com/last-minute-travel-deals/biggest-discount-offer?sortb=bestselling&offerFp=1&offerAuc=1&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=all-top-deals"><img name="Cont_16" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/title-topdeals-top.gif" /></a></td>
</tr>
<tr>

<td style="border-bottom:1px solid #9c9da0;border-left:1px solid #9c9da0;border-right:1px solid #9c9da0;color:#333333">
<table border="0" width="100%" valign="top" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td valign="top"><a name="0.1_section_TOP" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/last-minute-travel-deals/biggest-discount-offer?sortb=bestselling&offerFp=1&offerAuc=1&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=all-top-deals"><img name="Cont_17" alt="Top Deals" border="0" src="http://www.luxurylink.com/images/insider/images1/title-topdeals.gif" /></a></td>
<td align="right" valign="bottom" style="font-family:Arial, Verdana, sans-serif;font-size:13px;background-color: #ffffff;padding: 0px 20px 0px 0px;"><a xt="SPCLICK" name="www_luxurylink_com_fivestar_tr" style="color:#33669a; padding: 0px 0px 0px 450px;" target="_blank" href="http://www.luxurylink.com/last-minute-travel-deals/biggest-discount-offer?sortb=bestselling&offerFp=1&offerAuc=1&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=all-top-deals">View All Top Deals</a></td>
</tr>
</tbody>

</table>
<table align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif">
<tbody>
<tr>
<td width="20" rowspan="6"></td>
<td height="20" colspan="3"><img name="Cont_18" width="690" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-690.gif" /></td>
<td width="20" rowspan="6"></td>
</tr>
<tr>

<td width="325" style="font-size:11px" valign="top">

<? displayBox2($rows[4],$date_ymd,$version);?>

</td>
<td width="40"></td>

<td width="325" style="font-size:11px" valign="top">

<? displayBox2($rows[5],$date_ymd,$version);?>

</td>
</tr>

<tr>
<td height="30" colspan="3"><img name="Cont_18" width="690" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-690.gif" /></td>
</tr>
<tr>
<td width="325" style="font-size:11px" valign="top">

<? displayBox2($rows[6],$date_ymd,$version);?>

</td>
<td width="40"></td>
<td width="325" style="font-size:11px" valign="top">

<? displayBox2($rows[7],$date_ymd,$version);?>

</td>
</tr>
<tr>
<td height="30" colspan="3"></td>
</tr>
</tbody>

</table>
</td>
</tr>
</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td><img name="Cont_1" height="15" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>

</tr>

<tr>
<td><img name="Cont_1" height="15" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>
</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif;font-size:13px;background-color: #ffffff;">
<tbody>

<tr>
<td><img name="Cont_24" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/title-moretopdeals-top.gif" /></td>
</tr>
<tr>
<td style="border-bottom:1px solid #9c9da0;border-left:1px solid #9c9da0;border-right:1px solid #9c9da0;color:#333333">
<table border="0" width="100%" valign="top" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td valign="top"><a name="0.1_section_NEW_1" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/last-minute-travel-deals/biggest-discount-offer?sortb=bestselling&offerFp=1&offerAuc=1&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=all-top-deals">      <img name="Cont_25" alt="More Top Deals" border="0" src="http://www.luxurylink.com/images/insider/images1/title-moretopdeals.gif" /></a></td>

<td align="right" valign="bottom" style="font-family:Arial, Verdana, sans-serif;font-size:13px;background-color: #ffffff;padding: 0px 20px 0px 0px;"><a xt="SPCLICK" name="www_luxurylink_com_fivestar_tr" style="color:#33669a; padding: 0px 0px 0px 385px;" target="_blank" href="http://www.luxurylink.com/last-minute-travel-deals/biggest-discount-offer?sortb=bestselling&offerFp=1&offerAuc=1&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=all-top-deals">View All Top Deals</a></td>
</tr>
</tbody>
</table>
<table align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif">
<tbody>
<tr>
<td width="20" rowspan="6"></td>

<td height="20" colspan="3"><img name="Cont_18" width="690" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-690.gif" /></td>
<td width="20" rowspan="6"></td>
</tr>
<tr>
<td width="325" style="font-size:11px" valign="top">

<? displayBox2($rows[8],$date_ymd,$version);?>

</td>
<td width="40"></td>
<td width="325" style="font-size:11px" valign="top">

<? displayBox2($rows[9],$date_ymd,$version);?>

</td>
</tr>
<tr>
<td height="30" colspan="3"><img name="Cont_18" width="690" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-690.gif" /></td>
</tr>
<tr>

<td width="325" style="font-size:11px" valign="top">

<? displayBox2($rows[10],$date_ymd,$version);?>

</td>
<td width="40"></td>

<td width="325" style="font-size:11px" valign="top">

<? displayBox2($rows[11],$date_ymd,$version);?>

</td>
</tr>

<tr>
<td height="30" colspan="3"></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>

<table width="730" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td><img name="Cont_1" height="15" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>

<tr>
<td><img name="Cont_1" height="15" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>

</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif;font-size:13px;background-color: #ffffff;">
<tbody>
<tr>
<td><a xt="SPCLICK" name="www_luxurylink_com_fivest_15" target="_blank" href="http://www.luxurylink.com/fivestar/hotel-deals/Editor_Choice?sortb=bestselling&pg=1&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=all-editors-picks"><img name="Cont_30" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/title-editorspicks-top.gif" /></a></td>
</tr>
<tr>
<td style="border-bottom:1px solid #9c9da0;border-left:1px solid #9c9da0;border-right:1px solid #9c9da0;color:#333333">

<table border="0" width="100%" valign="top" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td valign="top"><a name="0.1_section_EDITORS" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/fivestar/hotel-deals/Editor_Choice?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=all-editors-picks"><img name="Cont_31" alt="Top Deals" border="0" src="http://www.luxurylink.com/images/insider/images1/title-editorspicks.gif" /></a></td>
<td align="right" valign="bottom" style="font-family:Arial, Verdana, sans-serif;font-size:13px;background-color: #ffffff;padding: 0px 20px 0px 0px;"><a xt="SPCLICK" name="www_luxurylink_com_fivest_16" style="color:#33669a; padding: 0px 0px 0px 401px;" target="_blank" href="http://www.luxurylink.com/fivestar/hotel-deals/Editor_Choice?sortb=bestselling&pg=1&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=all-editors-picks">View Editor's Picks</a></td>
</tr>
</tbody>
</table>

<table align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif">
<tbody>
<tr>
<td width="20" rowspan="8"></td>
<td height="20" colspan="3"><img name="Cont_18" width="690" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-690.gif" /></td>
<td width="20" rowspan="8"></td>
</tr>
<tr>
<td width="325" style="font-size:11px" valign="top">


<? displayEditorsPicksImg($rows[12],$date_ymd,$version)?>

</td>

<td width="25"></td>
<td width="340" style="font-size:12px;line-height:16px" valign="top">

<? displayEditorsPicksText($rows[12],$date_ymd,$version)?>

</td>
</tr>
<tr>
<td height="30" colspan="3"><img name="Cont_18" width="690" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-690.gif" /></td>
</tr>
<tr>
<td width="325" style="font-size:11px" valign="top">

<? displayEditorsPicksImg($rows[13],$date_ymd,$version)?>

</td>
<td width="25"></td>

<td width="340" style="font-size:12px;line-height:16px" valign="top">

<? displayEditorsPicksText($rows[13],$date_ymd,$version)?>

</td>

</tr>
<tr>
<td height="30" colspan="3"><img name="Cont_18" width="690" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-690.gif" /></td>
</tr>
<tr>
<td width="325" style="font-size:11px" valign="top">

<? displayEditorsPicksImg($rows[14],$date_ymd,$version)?>

</td>
<td width="25"></td>
<td width="340" style="font-size:12px;line-height:16px" valign="top">

<? displayEditorsPicksText($rows[14],$date_ymd,$version)?>

</td>
</tr>

<tr>
<td height="30" colspan="3"></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>

<table width="730" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td><img name="Cont_1" height="15" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>
</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif;font-size:13px; background-color: #ffffff;">
<tbody>

<tr>
<td><a name="0.1__section_MYSTERY_1" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/fivestar/hotel-deals/Editor_Choice?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=mystery"><img name="Cont_35" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/ads/insertions/mystery4.jpg" /></a></td>
</tr>
<tr>
<td style="border-bottom:1px solid #9c9da0;border-left:1px solid #9c9da0;border-right:1px solid #9c9da0;color:#333333"><a name="0.1_section_MYSTERY" target="_blank" xt="SPCLICK" href="http://www.luxurylink.com/last-minute-travel-deals/mystery-offer?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=mystery"><img name="Cont_36" alt="Top Deals" style="margin-right:395px" border="0" src="http://www.luxurylink.com/images/ads/insertions/mystery1.jpg" /></a>
<table align="center" cellpadding="0" cellspacing="0" style="font-family:Arial, Verdana, sans-serif">
<tbody>
<tr>
<td width="20" rowspan="8"></td>

<td height="20" colspan="3"><img name="Cont_18" width="690" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-690.gif" /></td>
<td width="20" rowspan="8"></td>
</tr>
<tr>
<td width="325" style="font-size:11px" valign="top"><a xt="SPCLICK" name="www_luxurylink_com_luxury_hote" target="_blank" href="http://www.luxurylink.com/luxury-hotels/mystery-hotel?isMystery=1&oid=948942&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=mystery">     <img name="Cont_37" width="325" height="182" style="display:block" alt="Mystery Auction" border="0" src="http://www.luxurylink.com/images/ads/insertions/mystery3.jpg" /></a></td>
<td width="25"></td>
<td width="340" style="font-size:12px;line-height:16px" valign="top"><strong style="font-size:13px">England - $1 Starting Bid<br />
<a xt="SPCLICK" name="www_luxurylink_com_luxury_hote" style="color:#33669a" target="_blank" href="http://www.luxurylink.com/luxury-hotels/mystery-hotel?isMystery=1&oid=948942&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=mystery">Mystery Auction</a></strong><br />

<br />
Hint: This city has the largest urban zone in the European Union.
<br />
<br />
The property name for this Mystery Auction will remain a secret, and for the moment so will the location...but only for the moment. We will be revealing clues throughout the course of the auction in our online Community.     <br />
<br />
<a xt="SPCLICK" name="www_luxurylink_com_luxury_hote" style="color:#33669a" target="_blank" href="http://www.luxurylink.com/luxury-hotels/mystery-hotel?isMystery=1&oid=948942&utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=mystery">     <img name="Cont_8" align="right" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/btn-viewpackage.gif" /></a></td>
</tr>

<tr>
<td height="30" colspan="3"><img name="Cont_18" width="690" height="1" alt="------" style="display:block" border="0" src="http://www.luxurylink.com/images/insider/images1/hr-690.gif" /></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>

<table width="730" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<td><img name="Cont_1" height="15" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>
</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0">
<tbody>

<tr>
<td><img name="Cont_1" height="15" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>
</tbody>
</table>
<table width="730" align="center" cellpadding="0" cellspacing="0" style="font-family:Verdana, Arial, sans-serif;font-size:11px;border:1px solid #9c9da0;background-color: #ffffff;">
<tbody>
<tr>
<td width="80"></td>

<td width="150" height="40" valign="middle"><a xt="SPCLICK" name="www_facebook_com_LuxuryLink" style="color:#336699;text-decoration:none" target="_blank" href="http://www.facebook.com/LuxuryLink">     <img name="Cont_38" align="left" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/icon-footer-facebook.gif" /> Like Us on<br />
Facebook</a></td>
<td width="160" height="40" valign="middle"><a xt="SPCLICK" name="twitter_com____LuxuryLink" style="color:#336699;text-decoration:none" target="_blank" href="http://twitter.com/#!/LuxuryLink">     <img name="Cont_39" align="left" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/icon-footer-twitter.gif" /> Luxury Link<br />
on Twitter</a></td>
<td width="160" height="40" valign="middle"><a xt="SPCLICK" name="www_luxurylink_com_community_f" style="color:#336699;text-decoration:none" target="_blank" href="http://www.luxurylink.com/community/forum.php?utm_medium=news&utm_term=<?=$version?>&utm_source=insider&utm_campaign=llinsider_<?=$date_ymd?>&utm_content=community">     <img name="Cont_40" align="left" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/icon-footer-luxurylink.gif" /> Visit our<br />

Community</a></td>
<td width="180" height="40" valign="middle"><a xt="SPEMAIL" name="feedback_luxurylink_com_subjec" target="_blank" style="text-decoration: none;color:#336699;" href="mailto:feedback@luxurylink.com?subject=Feedback">     <img name="Cont_41" align="left" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/icon-footer-feedback.gif" /><span style="text-decoration: none;color:#336699;"> Send Us<br />
your Feedback</span></a></td>
</tr>
</tbody>
</table>

<table width="730" align="center" cellpadding="0" cellspacing="0" style="font-family:Arial,Verdana, sans-serif;font-size:12px;line-height:16px;color:#666666">
<tbody>
<tr>
<td><img name="Cont_1" height="18" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>
<tr>
<td>Luxury Link<br />
5570 Lincoln Blvd. Los Angeles, CA 90094<br />

&copy; 2011 Luxury Link. All Rights Reserved.</td>
<td align="right">For information or assistance, call our Concierge at<br />
1 (888) 297-3299. Please do not reply to this email,<br />
you may unsubscribe <a name="Unsubscribe" style="color:#336699;text-decoration:underline" xt="SPCUSTOMOPTOUT" href="#SPCUSTOMOPTOUT">here</a>.  <a xt="SPCLICK" name="www_luxurylink_com_LL_home_prv" style="color:#336699" target="_blank" href="http://www.luxurylink.com/LL/home_prvcy2.php">Privacy Policy</a>.</td>

</tr>
<tr>
<td><img name="Cont_1" height="18" style="display:block" border="0" alt="" src="http://www.luxurylink.com/images/insider/images1/spacer.gif" /></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>

</table>
</div>
