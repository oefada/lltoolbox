<!-- This file is used only for debugging. See the controller if you want to modify the XLS file -->

<h2>Excel Package Export:</h2>

<?php $i = 0; ?>
<table>
    <tr>
        <td>Logo</td>
        <td>
            <?= $package['Package']['siteId'] == 1 ? 'Luxury Link' : ''; ?>
            <?= $package['Package']['siteId'] == 2 ? 'Family Getaway' : ''; ?>
        </td>
    </tr>
    <tr>
        <td>Client Name</td>
        <td><?= $client['name'] ?></td>
    </tr>
    <tr>
        <td>Client Id</td>
        <td><?= $client['clientId'] ?></td>
    </tr>
    <tr>
        <td>Location Display</td>
        <td><?= $client['locationDisplay'] ?></td>
    </tr>
    <tr>
        <td>URL</td>
        <td><?= $client['url'] ?></td>
    </tr>
    <tr>
        <td>Package Id</td>
        <td><?= $package['Package']['packageId'] ?></td>
    </tr>
    <tr>
        <td>Package Level</td>
        <td><?= ($package['Package']['isBarter'] ? 'Barter' : 'Remit') ?></td>
    </tr>
    <tr>
        <td>Package Created</td>
        <td><?= $package['Package']['created'] ?></td>
    </tr>
    <tr>
        <td>Working Title</td>
        <td><?= $package['Package']['packageName'] ?></td>
    </tr>
    <tr>
        <td>Room Nights</td>
        <td><?= $package['Package']['numNights'] ?></td>
    </tr>
    <tr>
        <td>Number of Guests</td>
        <td><?= $package['Package']['numGuests'] ?></td>
    </tr>
</table>

<h2>Inclusions</h2>

<?php foreach ($package['ClientLoaPackageRel'][0]['Inclusions'] as $inclusion): ?>
    <table>
        <tr>
            <td>Inclusion Name:</td>
            <td><?= $inclusion['LoaItem']['merchandisingDescription'] ?></td>
        </tr>
        <tr>
            <td>Price Per Night:</td>
            <td><?= $inclusion['LoaItem']['itemBasePrice'] ?></td>
        </tr>
        <tr>
            <td>Total Price:</td>
            <td><?= $inclusion['LoaItem']['totalPrice'] ?></td>
        </tr>
    </table>
<?php endforeach; ?>

<h2>Valid for Travel</h2>

<table>
    <tr>
        <td>
            <?php if (!empty($validity)) {
                foreach ($validity as $v) {
                    echo $v . '<br />';
                }
            }
            ?>
        </td>
    </tr>
</table>

<h2>Validity Blackout</h2>

<table>
    <tr>
        <td>
            <?php if (!empty($blackout)) {
                foreach ($blackout as $v) {
                    echo $v . '<br />';
                }
            }
            ?></td>
    </tr>
</table>

<h2>Blackout Weekdays</h2>

<table>
    <tr>
        <td>
            <?php if (!empty($bo_weekdays)) {
                echo $bo_weekdays;
            }
            ?></td>
    </tr>
</table>

<h2>Low Price Guarantees</h2>

<?php foreach ($lowPrice as $lp): ?>

    <table>
        <tr>
            <td>Date Ranges</td>
            <td><?= $lp['dateRanges'] ?></td>
        </tr>
        <tr>
            <td>Retail Value</td>
            <td><?= $lp['retailValue'] ?></td>
        </tr>
        <tr>
            <td>Auction Opening Bid</td>
            <td><?= $lp['auctionPrice'] ?></td>
        </tr>
        <tr>
            <td>Buy Now Price</td>
            <td><?= $lp['buyNowPrice'] ?></td>
        </tr>
        <tr>
            <td>Price Per Extra Night</td>
            <td><?= $lp['PricePoint']['pricePerExtraNight'] ?></td>
        </tr>
        <tr>
            <td>Range of Nights</td>
            <td><?= $package['Package']['flexNumNightsMin'] ?> - <?= $package['Package']['flexNumNightsMax'] ?>Nights
            </td>
        </tr>
    </table>

<?php endforeach; ?>

<h2>Terms and Conditions</h2>

<table>
    <tr>
        <td><?php echo $package['Package']['termsAndConditions']; ?></td>
    </tr>
</table>

<?php

/*
foreach (array('client', 'package', 'lowPrice') as $v) {
    echo '<hr/><h3>' . htmlentities($v) . '<h3>';
    echo '<pre>';
    echo htmlentities(print_r(${$v}, true));
    echo '</pre>';
}
*/

?>

<script type="text/javascript">
    jQuery(function () {
        var $ = jQuery;
        jQuery('table tr:nth-child(odd)').addClass('altrow');
    });
</script>
