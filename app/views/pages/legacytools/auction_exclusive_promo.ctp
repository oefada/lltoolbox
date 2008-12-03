<?
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('live');

$gRemoveAuctionId = (@$_GET['remove']) ? @$_GET['remove'] : false;
$gAddAuctionId = (@$_GET['add']) ? @$_GET['add'] : false;

// ADD
if ($gAddAuctionId) {
	$result = mysql_query("SELECT auctionId FROM auctionPromo WHERE offerId = $gAddAuctionId");
	if (!@mysql_num_rows($result)) {
		$result = mysql_query("INSERT INTO auctionPromo (offerId) values ($gAddAuctionId)");
	}
}

// REMOVE
if ($gRemoveAuctionId) {
	$result = mysql_query("DELETE FROM auctionPromo WHERE offerId = '$gRemoveAuctionId'");
}

?>

</HEAD>

<BODY>
	
	<div class="headerMainText">Affiliate Clearance Promo</div>
	
	<div class="marginBottomLargest">
		<form>
			<table class="table2" cellspacing="0"><tr>
				<td><b>Add Auction Id:</b></td><td><input type="text" name="add"/></td><td><input type="image" src="http://www.luxurylink.com/images/buttons/btn_sm_add_offer.gif" style="border:0px;"/></td>
			</tr></table>
		</form>
	</div>
	
	<div class="marginBottom">The following offers will be shown on the Promotion listing page: <a href="http://preview.luxurylink.com/travel-offers/listing.php?nav=promo" target="_blank">http://preview.luxurylink.com/travel-offers/listing.php?nav=promo</a></div>
	
	<table class="table" cellspacing="0">
		<tr>
			<th>&nbsp;</th><th>Auction Id</th><th>Product Id</th><th>Status</th><th>Auction Name</th><th>Product Name</th>
		</tr>
		<?
			$result = mysql_query(
				"SELECT *, (SELECT count(*) FROM offer WHERE offerId = a.offerId AND endDate < NOW()) AS is_closed FROM auctionPromo ap
					INNER JOIN offer a ON ap.offerId = a.offerId
					INNER JOIN client p ON a.clientId = p.clientId"
			);
			while ($row = mysql_fetch_assoc($result)) {
				$row['offerName'] = strip_tags($row['offerName']);
				$row['name'] = strip_tags($row['name']);
				$status = ($row['is_closed']) ? 'CLOSED' : 'LIVE';
				$row_class = ($row['is_closed']) ? 'grayRow' : '';
				$row_class = ($_GET['add'] == $row['offerId']) ? 'redRow' : $row_class;			
				echo "<tr class='$row_class'><td><a href='?remove=$row[offerId]'>Remove</a></td><td>$row[offerId]</td><td>$row[clientId]</td><td>$status</td><td>$row[offerName]</td><td>$row[name]</td></tr>";
			}
		?>
	</table>
	
</BODY>
</HTML>