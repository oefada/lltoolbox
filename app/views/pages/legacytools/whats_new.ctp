<?
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('live');

$gRemoveWhatsNewId = ($_GET['remove']) ? $_GET['remove'] : false;
$gAdd = ($_GET['addWhatsNew'] || $_GET['addAccolade']) ? true : false;

$gWhatsNewId = ($_GET['whatsNewId']) ? $_GET['whatsNewId'] : false;
$gInactive = ($_GET['inactive']) ? 1 : 0;
$gAuctionId = ($_GET['offerId']) ? $_GET['offerId'] : false;
$gProductId = ($_GET['clientId']) ? $_GET['clientId'] : false;
$gImageUrl = ($_GET['imageUrl']) ? $_GET['imageUrl'] : false;
$gImageLinkUrl = ($_GET['imageLinkUrl']) ? $_GET['imageLinkUrl'] : false;
$gCustomHTML = ($_GET['customHTML']) ? $_GET['customHTML'] : false;
$gAccoladeId = ($_GET['acc_id']) ? $_GET['acc_id'] : false;

// ADD
if ($gAdd) {
	if ($_GET['addAccolade']) {
		$type = 2;
	} else {
		$type = 3;
	}
	$result = mysql_query("INSERT INTO whatsNew(inactive, accoladeId, offerId, clientId, imageUrl, imageLinkUrl, customHtml, whatsNewTypeId) VALUES('$gInactive','$gAccoladeId','$gAuctionId','$gProductId','$gImageUrl','$gImageLinkUrl','$gCustomHTML', '$type')");
}

// REMOVE
if ($gRemoveWhatsNewId) {
	$result = mysql_query("DELETE FROM whatsNew WHERE whatsNewId = $gRemoveWhatsNewId");
}

// UPDATE
if ($gWhatsNewId) {
	$result = mysql_query("
		UPDATE whatsNew SET inactive = $gInactive, offerId = '$gAuctionId', clientId = '$gProductId', imageUrl = '$gImageUrl', imageLinkUrl = '$gImageLinkUrl', customHTML = '$gCustomHTML', accoladeId = '$gAccoladeId'
		WHERE whatsNewId = $gWhatsNewId
	");
	echo mysql_error();
}


?>	
	<div class="headerMainText">What's New</div>
	
	<div class="marginBottom">The following accolades, properties, offers, or links will be shown on the What's New module of the homepage: <a href="http://preview.luxurylink.com" target="_blank">http://preview.luxurylink.com</a></div>
	
	<p class="textRed textBold">ACCOLADES</p>

	<table class="table marginBottomLargest" cellspacing="0">
		<tr>
			<th>&nbsp;</th><th style="text-align:center">Inactive</th><th style="text-align:center">WhatsNew Id</th><th>Accolade Id</th><th>Source Name</th><th>Description</th><th>Date</th><th>&nbsp;</th>
		</tr>
		<?
			// UDDATE
			$result = mysql_query("SELECT w.*, a.accoladeId, a.description, a.accoladeDate, s.accoladeSourceId, s.accoladeSourceName FROM whatsNew w INNER JOIN accolade a ON w.accoladeId = a.accoladeId INNER JOIN accoladeSource s ON a.accoladeSourceId = s.accoladeSourceId WHERE w.whatsNewTypeId = 2 ORDER BY sortOrder");
			while ($row = mysql_fetch_array($result)) {
				$status = ($row['inactive']) ? 'INACTIVE' : 'LIVE';
				$row_class = ($row['inactive']) ? 'grayRow' : '';		
				echo "
					<form>
					<input type='hidden' name='updateAccolade' value='1'/>
					<input type='hidden' name='whatsNewId' value='$row[whatsNewId]'/>
					<tr class='$row_class'>
					<td><a href='?remove=$row[whatsNewId]'>Remove</a></td>
					<td align='center'><input type='checkbox' name='inactive' value='1'".($row[inactive]?' checked':'')."/></td>
					<td align='center'>$row[whatsNewId]</td>
					<td><input name='acc_id' value='$row[accoladeId]'/></td>
					<td>$row[accoladeSourceName]</td>
					<td>$row[description]</td>
					<td>$row[accoladeDate]</td>
					<td><input type='submit' value='Update'/></td>
					</tr>
					</form>
				";
			}
			
			// ADD NEW
			echo "
				<tr><td colspan='8' style='border-bottom:1px solid silver;'>&nbsp;</td></tr>
				<tr><td colspan='8'>&nbsp;</td></tr>
				<form>
				<input type='hidden' name='addAccolade' value='1'/>
				<tr>
				<tr class='$row_class'>
				<td><b>Add New</b></td>
				<td align='center'><input type='checkbox' name='inactive' value='1'".($row[inactive]?' checked':'')."/></td>
				<td align='center'>$row[whatsNewId]</td>
				<td><input name='acc_id' value='$row[acc_id]'/></td>
				<td>$row[source_name]</td>
				<td>$row[acc_desc]</td>
				<td>$row[acc_date]</td>
				<td><input type='submit' value='Add'/></td>
				</tr>
				</form>
			";
		?>
	</table>	
	
	
	
	<p class="textRed textBold">WHAT'S NEW</p>
	
	<table class="table" cellspacing="0">
		<tr>
			<th>&nbsp;</th><th style="text-align:center">Inactive</th><th style="text-align:center">WhatsNew Id</th><th>Auction Id</th><th>Auction Name</th><th>Product Id</th><th>Product Name</th><th>Image URL</th><th>Image Link URL</th><th>Custom HTML</th><th>&nbsp;</th>
		</tr>
		<?
			// UDDATE
			$result = mysql_query("SELECT w.*, p.name, p.locationDisplay, a.offerName, a.offerSubtitle FROM whatsNew w LEFT JOIN client p ON w.clientId = p.clientId LEFT JOIN offer a ON w.offerId = a.offerId WHERE w.whatsNewTypeId IN(1,3) ORDER BY sortOrder");
			while ($row = mysql_fetch_array($result)) {
				$row['offerName'] = strip_tags($row['offerName']);
				$row['name'] = strip_tags($row['name']);
				$status = ($row['inactive']) ? 'INACTIVE' : 'LIVE';
				$row_class = ($row['inactive']) ? 'grayRow' : '';			
				echo "
					<form>
					<input type='hidden' name='updateWhatsNew' value='1'/>
					<input type='hidden' name='whatsNewId' value='$row[whatsNewId]'/>
					<tr class='$row_class'>
					<td><a href='?remove=$row[whatsNewId]'>Remove</a></td>
					<td align='center'><input type='checkbox' name='inactive' value='1'".($row[inactive]?' checked':'')."/></td>
					<td align='center'>$row[whatsNewId]</td>
					<td><input name='offerId' value='$row[offerId]'/></td>
					<td>$row[offerName]</td>
					<td><input name='clientId' value='$row[clientId]'/></td>
					<td>$row[name]</td>
					<td><input name='imageUrl' value='$row[imageUrl]'/></td>
					<td><input name='imageLinkUrl' value='$row[imageLinkUrl]'/></td>
					<td><input name='customHtml' value='$row[customHtml]' size='50'/></td>
					<td><input type='submit' value='Update'/></td>
					</tr>
					</form>
				";
			}
			
			// ADD NEW
			echo "
				<tr><td colspan='11' style='border-bottom:1px solid silver;'>&nbsp;</td></tr>
				<tr><td colspan='11'>&nbsp;</td></tr>
				<form>
				<input type='hidden' name='addWhatsNew' value='1'/>
				<tr>
				<td><b>Add New:</b></td>
				<td align='center'><input type='checkbox' name='inactive' value='$row[inactive]'/></td>
				<td align='center'>$row[whatsNewId]</td>
				<td><input name='offerId' value='$row[offerId]'/></td>
				<td>$row[auction_name]</td>
				<td><input name='clientId' value='$row[clientId]'/></td>
				<td>$row[product_desc]</td>
				<td><input name='imageUrl' value='$row[imageUrl]'/></td>
				<td><input name='imageLinkUrl' value='$row[imageLinkUrl]'/></td>
				<td><input name='customHTML' value='$row[customHTML]' size='50'/></td>
				<td><input type='submit' value='Add'/></td>
				</tr>
				</form>
			";
		?>
	</table>