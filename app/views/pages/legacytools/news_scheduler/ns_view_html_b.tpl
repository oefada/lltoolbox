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
<div style="text-align:center;"><img src="http://www.luxurylink.com/images/ads/insertions/header15.GIF" alt="Top 15" border="0" style="margin:20px 0px 25px 0px;"/></div>
<!-- END STYLE HEADER -->

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top">
			<!-- OFFERS -->
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				{section name=segment loop=$segments}
				{if !$segments[segment].mailing_segment_html}
				{if !$smarty.section.segment.first}<tr><td colspan="3"><img src="http://www.luxurylink.com/images/shared/fancyDivider360.gif" border="0" alt="line" width="100%" height="3" style="margin:20px 0px;"/></td></tr>{/if}
				<tr>
					<td colspan="3" style="padding-bottom:10px;font:10px Verdana,Arial,Sans-Serif;color:#444444;">
						<div style="font-size:11px;margin-bottom:3px;"><a href="{$segments[segment].url}&{$utm_qs}" target="_blank" style="color:#336699;font-weight:bold;">{$segments[segment].title}</a></div>
						<div style="color:#806284;">{$segments[segment].subtitle}</div>
					</td>
				</tr>
				<tr>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;line-height:13px;" width="105">
						{if $segments[segment].offer_type_id!=3}<div><b><span style="color:black;">Retail Value:</span></b></div>{/if}
						<div {if $segments[segment].offer_type_id!=3}style="height:17px;border-bottom:1px solid silver;"{/if}><b><span style="color:#806284;">{if $segments[segment].offer_type_id==3}Special Price:{elseif $segments[segment].offer_type=='fp'}Your Price:{else}Minimum Bid:{/if}</span></div>
					</td>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;color:red;line-height:13px;padding-right:15px;" width="70" align="right">
						{if $segments[segment].offer_type_id!=3}<div><span style="color:black;">{if $segments[segment].offer_retail}${$segments[segment].offer_retail}{/if}</span></div>{/if}
						<div {if $segments[segment].offer_type_id!=3}style="height:17px;border-bottom:1px solid silver;"{/if}><span style="color:#806284;">{if $segments[segment].offer_minimum_bid}${$segments[segment].offer_minimum_bid}{/if}</span></div>
					</td>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;color:#666666;line-height:13px;border-left:1px solid silver;padding-left:15px;">{$segments[segment].copy}</td>
				</tr>
				<tr>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;line-height:13px;" width="105">
						{if $segments[segment].offer_type_id!=3}<div style="margin-top:3px;"><b><span style="color:#ce020b;">{if $segments[segment].offer_type=='fp'}Your Savings:{else}Savings Start at:{/if}</span></div>{/if}
					</td>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;color:red;line-height:13px;padding-right:15px;" width="70" align="right">
						<div style="margin-top:3px;">{if $segments[segment].offer_type_id!=3}<span style="color:#ce020b;">{if $segments[segment].offer_minimum_bid}${$segments[segment].offer_saving}{/if}</span>{/if}</div>
					</td>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;line-height:13px;border-left:1px solid silver;padding-left:15px;">
						 <div style="margin-top:3px;"><a href="{$segments[segment].url}&{$utm_qs}" target="_blank" style="color:#42c2dd;font-weight:bold;">{if $segments[segment].offer_type=='fp'}BUY NOW{else}BID NOW{/if}</a></div>
					</td>
				</tr>
				{else}
					{$segment.mailing_segment_html}
				{/if}
				{/section}
				<!-- MYSTERY AUCTION -->
				<tr><td colspan="3"><img src="http://www.luxurylink.com/images/shared/fancyDivider360.gif" border="0" alt="line" width="100%" height="3" style="margin:20px 0px;"/></td></tr>
				<tr><td colspan="3" style="padding-bottom:8px;font:11px Verdana,Arial,Sans-Serif;color:#444444;"><a href="http://www.luxurylink.com/auctions/auc_detail.php?id=&{$utm_qs}" target="_blank" style="color:#336699;font-weight:bold;">Mystery Auction - $1 Starting Bid</a></td></tr>
				<tr>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;line-height:13px;" width="105">
						<div><b><span style="color:black;">Retail Value:</span></b></div>
						<div style="height:17px;border-bottom:1px solid silver;"><b><span style="color:#806284;">Minimum Bid:</span></b></div>
					</td>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;color:red;line-height:13px;padding-right:10px;" width="70" align="right">
						<div><span style="color:black;">$00.00</span></div>
						<div style="height:17px;border-bottom:1px solid silver;"><span style="color:#806284;">$1.00</span></div>
					</td>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;color:#666666;line-height:13px;border-left:1px solid silver;padding-left:10px;">Blurb</td>
				</tr>
				<tr>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;line-height:13px;" width="105">
						<div style="margin-top:3px;"><b><span style="color:#ce020b;">Savings Start at:</span></b></div>
					</td>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;color:red;line-height:13px;padding-right:10px;" width="70" align="right">
						<div style="margin-top:3px;"><span style="color:#ce020b;">$00.00</span></div>
					</td>
					<td valign="top" style="font:10px Verdana,Arial,Sans-Serif;line-height:13px;border-left:1px solid silver;padding-left:10px;">
						<div style="margin-top:3px;"><a href="http://www.luxurylink.com/auctions/auc_detail.php?id=&{$utm_qs}" target="_blank" style="color:#42c2dd;font-weight:bold;">BID NOW</a></div>
					</td>
				</tr>
				<!-- END MYSTERY AUCTION -->
			</table>
			<!-- END OFFERS -->			
		</td>
		<td width="318" valign="top" style="font:10px Verdana,Arial,Sans-Serif;color:#444444;padding-left:20px;">
			<!-- BIG AD -->
			<table cellpadding="0" cellspacing="0" width="318" height="275" background="http://www.luxurylink.com/images/email/shadow_300x250.gif">
				<tr><td valign="top" style="padding:7px 0px 0px 7px;"><a href="" target="_blank"><img src="" width="300" height="250" border="0" alt="Ad"></a></td></tr>
			</table>
			<!-- END BIG AD -->

			<br/>
			
			<!-- LUXURY MARKETPLACE -->
			<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="background:#F0F0F0;border:1px solid silver;padding:10px;margin-bottom:15px;line-height:18px;font-size:11px;">
				<div><img src="http://www.luxurylink.com/images/email/hdr_txt_luxury_marketplace.gif" border="0" alt="Luxury Marketplace"/></div>
				<div><img src="http://www.luxurylink.com/images/shared/fancyDivider360.gif" border="0" alt="line" width="296" height="3" style="margin:5px 0px !important; margin:10px 0px;"/></div>					
				
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				{section name=segment max=3 loop=$segments}
				{if !$segments[segment].mailing_segment_html}
				<tr>
				<td background="http://www.luxurylink.com/images/shared/thumb_70x64_silver_shadow.gif" width="88" height="83" valign="top" style="padding:7px 0px 0px 7px;">
				<a href="{$segments[segment].url}&{$utm_qs}" target="_blank"><img src="http://www.luxurylink.com/images/por/{$segments[segment].product_id}/{$segments[segment].product_id}-list-sml-01.jpg" alt="{$segments[segment].alt_tag}" border="0"></a>
				</td>
				<td width="1"></td>
				<td valign="top" align="left" style="font:10px Verdana,Arial,Sans-Serif;color:#444444;line-height:12px;">
					<div style="">
						<div style="margin-bottom:3px;"><a href="{$segments[segment].url}&{$utm_qs}" target="_blank" style="color:#336699;font-weight:bold;">{$segments[segment].title}</a></div>
						<div style="margin-bottom:3px;">{$segments[segment].copy}</div>
						<div style="margin-bottom:20px;"><a href="{$segments[segment].url}&{$utm_qs}" target="_blank" style="color:#42c2dd;font-weight:bold;">LEARN MORE</a></div>
					</div>
				</td>
				</tr>
				{else}
					{$segment.mailing_segment_html}
				{/if}
				{/section}
				</table>
			</td></tr></table>
			<!-- END LUXURY MARKETPLACE -->			
		
			<br/><br/>
			
			<!-- SPONSORED LINKS -->
			<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="border:1px solid silver;padding:10px;margin-bottom:15px;font:11px Verdana,Arial,Sans-Serif;line-height:18px;">
				<div><img src="http://www.luxurylink.com/images/email/hdr_txt_sponsored_links.gif" border="0" alt="Sponsored Links"/></div>
				<div><img src="http://www.luxurylink.com/images/shared/fancyDivider360.gif" border="0" alt="line" width="296" height="3" style="margin:5px 0px !important; margin:10px 0px;"/></div>
				<a style="color:#336699;" href="http://www.luxurylink.com/travel-offers/listing.php?style=3&{$utm_qs}" target="_blank"><b>Escape to Mexico</b></a><br/>
				<a style="color:#336699;" href="http://www.luxurylink.com/travel-offers/listing.php?priceRange=1500&{$utm_qs}" target="_blank"><b>Luxury Offers Under $1,500</b></a><br/>
				<a style="color:#336699;" href="http://www.luxurylink.com/travel-offers/listing.php?nav=clo&offerAuc=1&auctionType=1-2-6&sortBy=5&{$utm_qs}" target="_blank"><b>Going, Going, Gone - Auctions Closing Soon</b></a>
			</td></tr></table>
			<!-- END SPONSORED LINKS -->
		</td>
	</tr>
</table>

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