<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Luxury Link</title>
</head>

<body style="text-align:center;font:10px Verdana,Arial,Sans-Serif;width:100%;color:#444444;">

<div style="text-align:center;width:750px;margin:auto;font:10px Verdana,Arial,Sans-Serif;color:#444444;">
This email was sent to you by Luxury Link. To ensure delivery to your inbox (not bulk or junk folders),<br/>
please add news@luxurylink.com to your address book. Having trouble viewing the email below?<br/>
<a style="color:#336699;" href="http://www.luxurylink.com/email/{$mailing_timestamp|date_format:"%m%d%y"}_{$smarty.get.mailing_schedule_id}.html" target="_blank">Click here!</a>
</div>

<br/>

<!--POST

var eMail = user.attr('eMail');

POST-->

<!-- BORDER -->
<div style="text-align:left;margin:auto;width:750px;padding:20px;border:1px solid silver;font:11px Verdana,Arial,Sans-Serif;">

<!--LOGO HEADER-->
<table cellpadding="0" cellspacing="0" align="center" border="0" width="100%" style="padding-bottom:5px;"><tr>
	<td align="right" width="450"><a href="http://www.luxurylink.com/?{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/shared/ll_logo_sm.gif" alt="Luxury Link" border="0"></a></td>
	<td align="right" valign="bottom" style="padding-bottom:4px;font:10px Verdana,Arial,Sans-Serif;">{$mailing_timestamp|date_format:"%B %e, %G"}</td>
</tr></table>
<!-- END LOGO HEADER-->

<meta name="WT.ll_pv" content="{$product_ids}">

<!-- START PRIMARY NAVIGATION -->
<table cellpadding="0" cellspacing="0" align="center" border="0" background="http://www.luxurylink.com/images/email/menu/menu_line.gif" height="22" width="100%" style="clear:both;margin-top:5px;"><tr><td align="center" valign="top" style="padding-top:1px;">
	<a href="http://www.luxurylink.com/LL/home.php?{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/email/menu/home.gif" border="0"/></a>
	<img src="http://www.luxurylink.com/images/email/menu/dot.gif"/>
	<a href="http://www.luxurylink.com/destinations/index.php?{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/email/menu/destinations.gif" border="0"/></a>
	<img src="http://www.luxurylink.com/images/email/menu/dot.gif"/>
	<a href="http://www.luxurylink.com/travel-offers/index.php?{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/email/menu/travel-offers.gif" border="0"/></a>
	<img src="http://www.luxurylink.com/images/email/menu/dot.gif"/>
	<a href="http://community.luxurylink.com/?{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/email/menu/community.gif" border="0"/></a>
</td></tr></table>
<!-- END PRIMARY NAVIGATION -->

<!-- STYLE HEADER -->
<div style="text-align:center;"><img src="http://www.luxurylink.com/images/email/style/53/53_header.gif" alt="Africa Header" border="0" style="margin:20px 0px 25px 0px;"/></div>
<!-- END STYLE HEADER -->

<!-- IMAGE HEADER -->
<div style="float:left; margin-top:3px; margin-right:10px; width:318px;">
	<table cellpadding="0" cellspacing="0" width="318" height="275" background="http://www.luxurylink.com/images/email/shadow_300x250.gif">
		<tr><td valign="top" style="padding:7px 0px 0px 7px;"><a href="http://www.luxurylink.com/travel-offers/listing.php?style=53&{$utm_qs}" style="color:#336699;font-weight:bold;"><img src="http://www.luxurylink.com/images/email/style/53/53_image.jpg" alt="Africa Image" border="0"/></a></td></tr>
	</table>
	<div style="text-align:center;"><a href="http://www.luxurylink.com/travel-offers/listing.php?style=53&{$utm_qs}" style="color:#336699;font-weight:bold;">View all Africa Offers</a></div>
</div>
<!-- END IMAGE HEADER -->

<!-- TOP ADS -->
<table border="0" cellpadding="0" cellspacing="0">
	{section name=segment max=3 loop=$segments}
	{if !$segments[segment].mailing_segment_html}
	<tr style="height:96px !important;height:90px;">
	<td background="http://www.luxurylink.com/images/shared/thumb_70x64_shadow.gif" width="85" valign="top" style="padding:6px 0px 0px 7px;">
		<a href="{$segments[segment].url}&{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/por/{$segments[segment].product_id}/{$segments[segment].product_id}-list-sml-01.jpg" alt="{$segments[segment].alt_tag}" border="0"></a>
	</td>
	<td width="5"></td>
	<td valign="top" align="left" style="line-height:12px;font:10px Verdana,Arial,Sans-Serif;color:#444444;">
		<div style="margin-bottom:3px;"><a href="{$segments[segment].url}&{$utm_qs}" target="_blank" style="color:#336699;font-weight:bold;">{$segments[segment].title}</a></div>
		<div style="margin-bottom:3px;color:#806284;">{$segments[segment].title_header}</div>
		<div>{$segments[segment].copy}</div>
	</td>
	</tr>
	{else}
		{$segment.mailing_segment_html}
	{/if}
	{/section}
</table>
<!-- END TOP ADS -->

<div style="clear:both;"></div>
<br/><br/><br/>

<!-- BIG AD -->
<div style="float:right; margin-top:0px !important; margin-top:3px; margin-left:10px; width:318px;">
	<table cellpadding="0" cellspacing="0" width="318" height="275" background="http://www.luxurylink.com/images/email/shadow_300x250.gif">
		<tr><td valign="top" style="padding:7px 0px 0px 7px;"><a href="" target="_blank"><img src="" width="300" height="250" border="0" alt="Ad"></a></td></tr>
	</table>
</div>
<!-- END BIG AD -->

<!-- TOP 3 OFFERS -->
<table border="0" cellpadding="0" cellspacing="0">
	{section name=segment max=3 loop=$segments}
	{if !$segments[segment].mailing_segment_html}
	<tr height="90">
	<td background="http://www.luxurylink.com/images/shared/thumb_70x64_shadow.gif" width="85" valign="top" style="padding:6px 0px 0px 7px;">
		<a href="{$segments[segment].url}&{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/por/{$segments[segment].product_id}/{$segments[segment].product_id}-list-sml-01.jpg" alt="{$segments[segment].alt_tag}" border="0"></a>
	</td>
	<td width="5"></td>
	<td valign="top" align="left" style="line-height:12px;font:10px Verdana,Arial,Sans-Serif;color:#444444;">
		<div style="margin-bottom:3px;"><a href="{$segments[segment].url}&{$utm_qs}" target="_blank" style="color:#336699;font-weight:bold;">{$segments[segment].title}</a></div>
		<div style="margin-bottom:3px;color:#806284;">{$segments[segment].title_header}</div>
		<div>{$segments[segment].copy}</div>
	</td>
	</tr>
	{else}
		{$segment.mailing_segment_html}
	{/if}
	{/section}
</table>
<!-- END TOP 3 OFFERS -->

<br/>

<!-- SPONSORED LINKS -->
<div align="center">
<table cellspacing="0" cellpadding="0" border="0"><tr>
<td style="font:10px Verdana,Arial,Sans-Serif;line-height:18px;clear:both;background:#F0F0F0;border:1px solid silver;padding:10px;" align="center">
	<div style="line-height:12px;padding-bottom:10px;margin-bottom:10px;border-bottom:1px solid silver;">
		<img src="http://www.luxurylink.com/images/email/hdr_txt_sponsored_links" border="0" alt="Sponsored Links"/>
		<!--<br/><img src="http://www.luxurylink.com/images/shared/fancyDivider360.gif" border="0" alt="line" width="100%" height="3" style="margin:3px 0px;"/>-->
	</div>
	<div><a style="color:#336699;" href="http://www.luxurylink.com/travel-offers/listing.php?style=3&{$utm_qs}" target="_blank"><b>Escape to Mexico - Bid Now!</b></a></div>
	<div><a style="color:#336699;" href="http://www.luxurylink.com/travel-offers/listing.php?nav=clo&offerAuc=1&auctionType=1-2-6&sortBy=5&{$utm_qs}" target="_blank"><b>Going, Going, Gone - Auctions Closing Soon</b></a></div>	
	<div><a style="color:#336699;" href="http://www.luxurylink.com/travel-offers/listing.php?priceRange=1500&{$utm_qs}" target="_blank"><b>Luxury Offers Under $1,500</b></a></div>
</td>
</tr></table>
</div>

<!-- END SPONSORED LINKS -->

<br/><br/>

<!-- OFFERS -->
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
	{section name=segment start=3 loop=$segments}
	{if !$segments[segment].mailing_segment_html}
	{if $smarty.section.segment.iteration % 2 == 1 && !$smarty.section.segment.first}
	</tr>
	<tr><td colspan="3"><img src="http://www.luxurylink.com/images/shared/fancyDivider740.gif" border="0" alt="line" style="margin:7px 0px 13px 0px;" width="100%" height="3"/></td></tr>
	<tr>
	{/if}
	<td width="33.3%" valign="top" style="padding-right:5px;">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td background="http://www.luxurylink.com/images/shared/thumb_70x64_shadow.gif" width="85" height="83" valign="top" style="padding:6px 0px 0px 7px;">
				<a href="{$segments[segment].url}&{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/por/{$segments[segment].product_id}/{$segments[segment].product_id}-list-sml-01.jpg" alt="{$segments[segment].alt_tag}" border="0"></a>
			</td>
			<td width="5">
			</td>
			<td valign="top" align="left" style="line-height:12px;font:10px Verdana,Arial,Sans-Serif;color:#444444;">
				<div style="margin-bottom:3px;"><a href="{$segments[segment].url}&{$utm_qs}" target="_blank" style="color:#336699;font-weight:bold;">{$segments[segment].title}</a></div>
				<div style="margin-bottom:3px;color:#806284;">{$segments[segment].title_header}</div>
				<div>{$segments[segment].copy}</div>
			</td>
			</tr>
		</table>
	</td>
	{else}
		{$segment.mailing_segment_html}
	{/if}
	{/section}
	</tr>
</table>
<!-- END OFFERS -->

<br/><br/>

<!-- ADVERTISEMENT -->
<table cellpadding="0" cellspacing="13" border="0" bgcolor="#F0F0F0" width="100%" style="clear:both;"><tr><td>
	<table border="0" cellspacing="0" cellpadding="0">
		<tr><td background="http://www.luxurylink.com/images/shared/thumb_70x64_silver_shadow.gif" width="90" height="83" valign="top" style="padding:7px 0px 0px 7px;">
			<a href="http://www.luxurylink.com/auctions/auc_detail.php?id=&{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/por/111530/111530-list-sml-01.jpg" border="0" alt="Advertisement"/></a>
		</td>
		<td style="font:11px Verdana,Arial,Sans-Serif;color:#444444;" valign="top">
			<div style="margin-bottom:5px;"><a href="http://www.luxurylink.com/auctions/auc_detail.php?id=&{$utm_qs}" style="color:#336699;font-weight:bold;">Mystery Auction - $1 Starting Bid</a></div>
			<div style="margin-bottom:5px;color:#806284;">Subtitle</div>
			<div>Blurb...</div>
		</td></tr>
	</table>
</td></tr></table>
<!-- END ADVERTISEMENT -->

</div><!-- END BORDER -->

<br/>
<div style="text-align:center;width:750px;margin:auto;font:10px Verdana,Arial,Sans-Serif;color:#444444;">
<!---UNSUBSCRIBE-->
<p>This email was sent to you because you subscribed to the semi-weekly Luxury Link newsletter. Please do not reply to this e-mail, as we are not able to respond to messages sent to this address. To unsubscribe from the Luxury Link newsletter <a style="color:#336699;" href="http://www.luxurylink.com/LL/unsubscribe.php?email=`eMail`&list=news" target="_blank">click here</a>.</p>
<!---END UNSUBSCRIBE-->
<!---PRIVACY NOTICE-->
<p><span style="color:#806284;font-weight:bold;">Privacy Notice:</span> Rest assured your email address will remain confidential and will never be shared with any other company. This service is private and complimentary, and you may unsubscribe at any time. For more information, please call our Client Services and Support Desk at 310-215-8060.</p>
<!---END PRIVACY NOTICE-->
<!---ADDRESS-->
<p style="color:gray;">Sent from Luxury Link, 5200 West Century Boulevard, Suite 410<br/>Los Angeles, CA 90045, Attn: E-mail Coordinator</p>
<!---END ADDRESS-->
</div>