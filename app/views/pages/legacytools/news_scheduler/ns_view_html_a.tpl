<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Luxury Link</title>
</head>

<body>


<table align="center">

<tr>
<td align="center" style="font:10px verdana,arial,sans-serif;">This email was sent to you by Luxury Link. To ensure delivery to your inbox (not bulk or junk folders),<br/>
please add news@luxurylink.com to your address book. Having trouble viewing the email below?<br/>
<a style="color:#806284;" href="http://www.luxurylink.com/email/{$mailing_timestamp|date_format:"%m%d%y"}_{$smarty.get.mailing_schedule_id}.html" target="_blank">Click here!</a>
</td></tr>

<tr><td height="15"></td></tr>

<tr><td>
<!--EMAIL BORDER-->
<table width="563" cellpadding="20" cellspacing="0" border="1" align="center" bordercolor="silver">
<tr>
<td align="center">


<!--EMAIL CONTENT-->

<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">


<tr><td align="center">
<!--LOGO HEADER-->
<table cellpadding="0" cellspacing="0" width="100%" height="46" border="0">
	<tr>
		<td align="left"><a href="http://www.luxurylink.com" target="_blank"><img src="http://www.luxurylink.com/images/email/LL_logo-V3.jpg" width="212" height="90" alt="Luxury Link" border="0"></a></td>
		<td align="right" valign="bottom"><font size="1" color="#919191" face="verdana, arial, sans-serif"><b>{$mailing_timestamp|date_format:"%B %e, %G"}</b></font></td>
	</tr>
</table>
<!-- END LOGO HEADER-->

</td></tr>


<tr><td height="15"></td></tr>


<tr><td>
<!-- START PRIMARY NAVIGATION -->
<table cellpadding="0" cellspacing="0" align="center" border="0" background="http://www.luxurylink.com/images/ads/email/menu_line.gif" height="22" width="100%">
<tr>
<td align="center">
<a href="http://www.luxurylink.com/LL/home.php?WT.mc_id=newsletterHome" target="_blank"><img src="http://www.luxurylink.com/images/ads/email/new_home.gif" border="0"/></a>
<img src="http://www.luxurylink.com/images/ads/email/dot.gif"/>
<a href="http://www.luxurylink.com/destinations/index.php?WT.mc_id=newsletterDestinations" target="_blank"><img src="http://www.luxurylink.com/images/ads/email/new_destinations.gif" border="0"/></a>
<img src="http://www.luxurylink.com/images/ads/email/dot.gif"/>
<a href="http://www.luxurylink.com/travel-offers/index.php?WT.mc_id=newsletterTravelOffers" target="_blank"><img src="http://www.luxurylink.com/images/ads/email/new_travel-offers.gif" border="0"/></a>
<img src="http://www.luxurylink.com/images/ads/email/dot.gif"/>
<a href="http://www.luxurylink.com/luxe/luxe_main.php?WT.mc_id=newsletterLuxeLife" target="_blank"><img src="http://www.luxurylink.com/images/ads/email/new_luxelife.gif" border="0"/></a>
<img src="http://www.luxurylink.com/images/ads/email/dot.gif"/>
<a href="http://www.luxurylink.com/portfolio/worlds_best/wb_main.php?WT.mc_id=newsletterWorldsBest" target="_blank"><img src="http://www.luxurylink.com/images/ads/email/new_worlds_best.gif" border="0"/></a>
<img src="http://www.luxurylink.com/images/ads/email/dot.gif"/>
<a href="http://community.luxurylink.com/?WT.mc_id=newsletterCommunity" target="_blank"><img src="http://www.luxurylink.com/images/ads/email/new_community.gif" border="0"/></a>
</td>
</tr>
</table>
<!-- END PRIMARY NAVIGATION -->
</td></tr>


<tr><td height="15"></td></tr>


<tr><td>
<!--3 HEADER IMAGES and LINKS-->
<table cellpadding="0" cellspacing="0" align="center" border="0">
	<tr align="center">
{foreach from=$segments item=segment}
{if $segment.mailing_segment_position_id == 1}
	<td background="http://www.luxurylink.com/images/shared/news_header_shadow.gif" width="178" height="124" valign="top" style="font:10px verdana,arial,sans-serif;">
	<br/>
	<a href="{$segment.url}&WT.mc_id=a1" target="_blank"><img src="http://www.luxurylink.com/images/por/{$segment.product_id}/{$segment.product_id}-news-lrg-01.jpg" width="154" height="85" alt="{$segment.alt_tag}"  border="0"></a><br/>
	<a href="{$segment.url}&WT.mc_id=a1" target="_blank" style="color:#806284;text-decoration:none;line-height:16px;">{$segment.title_header}</a>
	</td>
{/if}	
{/foreach}
	</tr>	
	</table>
<!--END 3 HEADER IMAGES-->
</td></tr>


<tr><td height="15"></td></tr>


<tr><td>
<!--ANCHOR BORDER-->
<table>
<tr> 
<td><img src="http://www.luxurylink.com/images/ads/header_standard.gif" width="519" height="47"></td>
</tr>
</table>
<!---END ANCHOR BORDER-->
</td></tr>

<meta name="WT.ll_pv" content="{$product_ids}">

<tr><td height="15"></td></tr>


<tr>
<td>
<!---ANCHOR-->

<table cellpadding="0" cellspacing="7" width="519" border="0" align="center">
<tr>
<td align="left" valign="top" style="font:10px verdana,arial,sans-serif;">
Looking for Value? Check out Luxury Link�s new and improved <a  style="color:#806284;" href="http://www.luxurylink.com/fixedprice/fp_listing.php?nav=hot&style=45&WT.mc_id=HomePgHotDeals" target="_blank">Hot Travel Deals</a> and <a style="color:#806284;" href="http://www.luxurylink.com/auctions/auc_listing.php?nav=hot&style=45&WT.mc_id=HomePgHotAuctions" target="_blank">Hot Travel Auctions</a>.  Here, we present the best deals on the website as determined by other Luxury Link users.
<a style="color:#806284;" href="http://ad.doubleclick.net/clk;75749114;10986294;u?http://family-travel.tauck.com/tours/europe-tours/great-britain-and-ireland-tours/paris-tour-yl-2007?WT.srch=1&WT.mc%20id=LL_YL" target="_blank"><b>Learn more</b></a>
</td>
<td align="right" width="120">
<a href="http://www.luxurylink.com/auctions/auc_listing.php?nav=hot&style=45&WT.mc_id=HomePgHotAuctions" target="_blank">
<img src="http://www.luxurylink.com/images/ads/anchor_pic1.jpg" border="0"></a>
</td>
</tr>
</table>

<!---END ANCHOR-->
</td>
</tr>


<tr><td height="15"></td></tr>


<tr><td>
<!---OFFER TABLE-->
<table border="0" width="519">


{foreach from=$segments item=segment}
<!---OFFER-->
<tr>
{if !$segment.mailing_segment_html}
<td background="http://www.luxurylink.com/images/shared/thumb_fpo_70x64_shadow.gif" width="82" height="78" align="center" valign="middle">
<a href="{$segment.url}" target="_blank">
<img src="http://www.luxurylink.com/images/por/{$segment.product_id}/{$segment.product_id}-list-sml-01.jpg" alt="{$segment.alt_tag}" border="0"></a>
</td>
<td width="5">
</td>
<td valign="top" align="left" style="font:10px verdana,arial,sans-serif;">
<div style="line-height:15px;">
	<div><a href="{$segment.url}" target="_blank" style="color:#806284;font-weight:bold;">{$segment.title}</a></div>
	<div style="color:#336699">{$segment.blurb}</div>
	<div style="color:#000000">{$segment.copy}</div>
	<div><a href="{$segment.url}" target="_blank" style="color:#806284;font-weight:bold;">{$segment.link_label}</a></div>
</div>
</td>
</tr>
<tr><td height="15"></td></tr>
{else}

{$segment.mailing_segment_html}

{/if}
			<!--END OFFER-->
{/foreach}





<!---3 TEXT LINKS-->	
<tr><td colspan="3">

<table bgcolor="#e9e9e9" border="0" cellpadding="0" width="100%" height="64">
<tr>

<td>

<table bgcolor="white" border="0" cellpadding="0" width="100%" height="62">
<tr>
<td>

<table bgcolor="#e9e9e9" border="0" width="100%" align="center" height="60">
<tr align="center">
<td width="33.3%" style="font:11px verdana,arial,sans-serif;"><a style="color:#806284;" href="http://www.luxurylink.com/portfolio/worlds_best.php?nav=wb" target="_blank"><b>World�s Best Hotels</b><br/>
<b>2007 � Winners</b></a>
</td>

<td width="33.3%" style="font:11px verdana,arial,sans-serif;"><a style="color:#806284;" href="http://www.luxurylink.com/luxe/luxe_wine_listing.php?nav=wine" target="_blank"><b>Wine at Auction</b><br/>
<b>Save up to 50% </b></a>
</td>

<td width="33.3%" style="font:11px verdana,arial,sans-serif;"><a style="color:#806284;" href="http://www.luxurylink.com/search/ser_offer_results.php?save=1&destination=53" target="_blank"><b>Safari in Africa</b><br/>
<b>On Sale Now!</b></a>
</td>
</tr>
</table>

</td>
</tr>
</table>

</td>
</tr>
</table>

</td></tr>
<!---END 3 TEXT LINKS-->	


<tr><td height="15"></td></tr>

<!---LL TEXT LINKS-->	
<tr><td colspan="3">

<table bgcolor="#e9e9e9" border="0" cellpadding="0" width="100%" height="46">
<tr>
<td>

<table bgcolor="white" border="0" cellpadding="0" width="100%" height="42">
<tr>
<td>

<table bgcolor="#e9e9e9" width="100%" align="center" height="40">
<tr align="center">
<td width="100%" style="font:11px verdana,arial,sans-serif;">
<a style="color:#806284;" href="http://www.luxurylink.com/luxe/luxe_merch_listing.php?nav=luggage_and_bags&prodType=23&WT.mc_id=LuxeLifeEmailtxt" target="_blank"><b>Designer Handbags � Marc Jacobs, Prada � On LuxeLife NOW</a>
</td>
</tr>
</table>

</td>
</tr>
</table>

</td>
</tr>
</table>

</td></tr>
<!---END LL TEXT LINKS-->	


<!---END OFFER TABLE-->
</table>

</td></tr>


<tr><td height="20"></td></tr>



<tr>
<td align="center" style="font:10px verdana,arial,sans-serif;">

<!---UNSUBSCRIBE-->
<p>You received this email because you subscribed to the semi-weekly Luxury Link<br/>
newsletter. To unsubscribe from Luxury Link news, simply
<a style="color:#806284;" href="http://blaster.luxurylink.com/u?id=%%memberidchar%%&l=news&c=T&n=F&o=%%outmail.messageid%%&u=http://www.luxurylink.com/LL/unsubscribe.php" target="_blank">click here</a>.
</p>
<!---END UNSUBSCRIBE-->

<!---PRIVACY NOTICE-->
<p>
<span style="color:#336699;font-weight:bold;">Privacy Notice:</span> Rest assured your email address will remain confidential and will<br/> 
never be shared with any other company. This service is private and <br/>
complimentary, and you may unsubscribe at any time. For more information,<br/>
please call our Client Services and Support Desk at 310-215-8060.
</p>
<!---END PRIVACY NOTICE-->

<!---ADDRESS-->
<p style="color:gray;">
Sent from Luxury Link, 5200 West Century Boulevard, Suite 410<br/> 
Los Angeles, CA 90045, Attn: E-mail Coordinator
</p>
<!---END ADDRESS-->

</td></tr>
</table>
<!---END EMAIL CONTENT-->


</td></tr>
</table>
<!---END EMAIL BORDER-->


</td></tr>
</table>
