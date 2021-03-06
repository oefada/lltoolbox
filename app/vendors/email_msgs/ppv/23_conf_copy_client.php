<?
include('../vendors/email_msgs/email_includes/header.html');

//set_include_path(get_include_path() . PATH_SEPARATOR . '/home/'.$_SERVER['ENV_USER'].'/luxurylink/php/includes/php' . PATH_SEPARATOR . 'includes/php' . PATH_SEPARATOR . '/var/www/luxurylink/php/includes/php');
//include("flex_functions.php");
?>

<h1 style="color:#<?=$siteHeader;?>;font-size:16px;margin:0px;padding:0px;margin-bottom:30px;">Reservation Confirmed</h1>

<p>
<span style="font-weight:bold;">User Account ID:</span>  <?php echo $userId;?><br />
<span style="font-weight:bold;">Ticket Reference ID:</span>  <?php echo $ticketId;?>
<br /><br />
</p>

<p>Dear <?php echo $clientName;?>, </p>

<table cellpadding="10" cellspacing="0" border="0" style="width:100%;background-color:#E0E0CD;margin-top:15px;margin-bottom:15px;">
	<tr>
		<td width="245" align="left" valign="top">
			<div style="width: 245px; height: 189px; padding: 8px 12px 12px 8px; background: url(http://www.luxurylink.com/images/shared/shadow-gal-lrg-light-olive.gif) no-repeat top left; overflow: hidden;"><img width="225" style="max-width: 225px;" src="<?php echo $clientImagePath; ?>" alt="<?php echo $packageName;?>" border="0" /></div>
		</td>
		<td width="100%" align="left" valign="top">
			<div style="color:#<?=$siteHeader;?>;font-size:16px;font-weight:bold;"><?php echo $clientNameP;?></div>
			<div style="color:#444444;font-size:12px;"><?php echo $locationDisplay;?></div><br />
			<!--<div style="color:#<?=$siteHeader;?>;font-size:12px;"><?php echo $packageName;?></div>-->
			<div style="height:15px;margin-top:15px;border-top:1px solid #B0B0B0;">&nbsp;</div>
			<div style="color:#<?=$siteHeader;?>;font-weight:bold;font-size:12px;margin-bottom:15px;">Reservation Details:</div>
			<table cellspacing="0" cellpadding="5" border="0">
			<tr>
				<td style="color:#000000;font-size:12px; font-weight:bold;" valign="top" align="left" width="160">Guest Name:</td>
				<td style="color:#444444;font-size:12px;" valign="top" align="left"><?php echo $userFirstName . ' ' . $userLastName;?></td>
			</tr>
			<tr>
				<td style="color:#000000;font-size:12px; font-weight:bold;" valign="top" align="left" width="160">Confirmation #:</td>
				<td style="color:#444444;font-size:12px;" valign="top" align="left"><?php echo $resConfNum;?></td>
			</tr>

			<tr>
				<td style="color:#000000;font-size:12px; font-weight:bold;" valign="top" align="left" width="160">No. of Nights:</td>
				<td style="color:#444444;font-size:12px;" valign="top" align="left"><?=$numNights;?></td>
			</tr>

				<?  

				if ($liveOfferData['isFlexPackage']==1){
					$arr = array(); 
					?>
					<tr>
						<td style='font-weight:bold;font-size:12px;'>
						<!--Total <?= $arr['num_nights'];?>-Night<br>-->Package Price:
						</td>
						<td style='font-size:12px;'>
						$<?= number_format($ticketData['billingPrice']);?>
						</td>
					</tr>
					<?php if ($arr['orig_num_nights']): ?>
					<tr>
						<td> &nbsp; </td>
						<td valign="top" align="left" style="font-weight:normal;font-size:12px;">
						(<?= $arr['orig_num_nights'];?>-Night Package Price: <?= $arr['orig_price'];?>
						<br>
						<?= $arr['price_expl'];?>)
						</td>
					</tr><?php endif; ?>
				<?}else{?>
					<tr>
					<td valign="top" align="left" style="font-size:12px;font-weight:bold;" nowrap>
					<?  if ($offerData['isAuction']==1){?>
						Winning Bid:
					<? }else{ ?>
						Rate:
					<? } ?>
					</td>
					<td valign="top" align="left" style='font-size:12px;'>
					$<?= number_format($ticketData['billingPrice']);?>
					</td>
					</tr>
				<? } ?>

			<tr>
				<td style="color:#000000;font-size:12px; font-weight:bold;" valign="top" align="left" width="160">Check-in:</td>
				<td style="color:#444444;font-size:12px;" valign="top" align="left"><?php echo $resArrivalDate;?></td>
			</tr>
			<tr>
				<td style="color:#000000;font-size:12px; font-weight:bold;" valign="top" align="left" width="160">Check-out:</td>
				<td style="color:#444444;font-size:12px;" valign="top" align="left"><?php echo $resDepartureDate;?></td>
			</tr>
			<? if (!$isAuction){ ?>
			<tr>
				<td style="color:#000000;font-size:12px; font-weight:bold;" valign="top" align="left" width="160">Special Requests:</td>
				<td style="color:#444444;font-size:12px;" valign="top" align="left"><?=$fpNotes;?></td>
			</tr>
			<? }  ?>
			</table>
		</td>
	</tr>
</table>

<p><?php echo $packageIncludes;?></p>
<p><?php echo $validityNote;?></p>
<p><?php echo $legalText;?></p>

<?php if ($resData[0]['reservation']['confirmationNotes']) :?>
<div style="padding:15px;background-color:#E4E4E4;margin-bottom:15px;">
	<p><strong>Notes from the property</strong></p>
	<p><?=nl2br($resData[0]['reservation']['confirmationNotes']);?></p>
</div>
<?php endif;?>

<div style="padding:15px;background-color:#E4E4E4;margin-bottom:15px;">
	<div style="color:#<?=$siteHeader;?>;"><strong>Please contact the property directly for any special requests.</strong><br /><br /></div>
	<?php foreach ($clients as $k => $client) : ?>
		<div>
		<?php echo $client['name'];?><br />
		<?php echo $client['address1'] ;?><br />
		<?php echo $client['address2'] ;?><br />
		<?php echo $client['city'] . ' ' .  $client['state'] . ' , ' , $client['country'] . ' ' . $client['postalCode'];?><br /><br />
		<?php echo $client['contacts'][0]['ppv_name'];?><br />

		Telephone: <?php echo $client['estaraPhoneLocal'];?><br />
		<?php if ($client['contacts'][0]['ppv_fax']):?>Facsimile: <?php echo $client['contacts'][0]['ppv_fax'];?><br /><?php endif;?>
		Email: <?php echo $client['contacts'][0]['ppv_email_address'];?>
		</div>
	<?php endforeach; ?>
</div>

<?php if (!$isAuction) : ?>	
	<p>
	Cancellation Policy
	<ul>
		<li>Cancellation up to 15 days prior to trip entails a penalty fee of US $35 plus any additional hotel penalties.</li>
		<li>Cancellation that is between the day of departure and 14 days prior to entails a penalty fee of US $100 plus any additional hotel penalties.</li>
		<li>A customer "No Show" could result in a forfeiture of entire package amount plus any additional hotel penalty.</li>
	</ul>
	</p>
<?php endif;?>

<?php if ($isAuction) : ?>
	<p><strong class="color:#<?=$siteHeader;?>;">As stated in our rules section, this package is non-refundable.</strong></p>
<?php endif;?>

<p>
If you have any questions or additional needs, please contact our Travel Concierge at <?=$sitePhone;?> or <?=$sitePhoneLocal;?>.
</p>

<br />
<p>Warm Regards,</p>
<p><?=$siteName;?></p>

</div>
</body>
</html>
