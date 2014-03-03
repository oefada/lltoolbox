<div class="tickets view">
<h2 class="title">Pegasus Booking Detail</h2>

<div class="ticket-table">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td width="200"><strong>Booking Id</strong></td>
    <td><?php echo $booking['PgBooking']['pgBookingId']; ?></td>
</tr>
<tr>
    <td width="200"><strong>TLD</strong></td>
    <td><strong><?php echo ($booking['PgBooking']['tldId'] == 2) ? '.CO.UK' : '.COM'; ?></strong></td>
</tr>
<tr>
    <td width="200"><strong>Created</strong></td>
    <td><?php echo $booking['PgBooking']['dateCreated']; ?></td>
</tr>
<tr>
    <td width="200"><strong>Client</strong></td>
    <td><?php echo $booking['Client']['name']; ?></td>
</tr>
<tr>
    <td width="200"><strong>User</strong></td>
    <td><?php echo $booking['User']['firstName']; ?> <?php echo $booking['User']['lastName']; ?></td>
</tr>
<tr>
    <td width="200"><strong>Traveler</strong></td>
    <td><?php echo $booking['PgBooking']['travelerFirstName']; ?> <?php echo $booking['PgBooking']['travelerLastName']; ?></td>
</tr>
<tr>
    <td width="200"><strong>Status</strong></td>
    <td><?php echo $booking['PgBooking']['pgBookingStatusId']; ?></td>
</tr>
</table>
</div>
<div style="clear:both;"></div>

<br/>
<div class="collapsible">
    <div class="handle"><?php __("Payments (" . sizeof($booking['PgPayment']) . ")"); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (sizeof($booking['PgPayment']) > 0): ?>
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <th style="text-align:center;">Id</th>
                    <th style="text-align:center;">Currency</th>
                    <th style="text-align:center;">Amount USD</th>
                    <th style="text-align:center;">Amount TLD</th>
                    <th style="text-align:center;">Type</th>
                </tr>
                <?php foreach ($booking['PgPayment'] as $k => $v) : ?>
                    <tr>
                        <td style="text-align:center;"><?= $v['pgPaymentId']; ?></td>
                        <td style="text-align:center;"><?= $v['currencyId']; ?></td>
                        <td style="text-align:center;"><?= $v['paymentUSD']; ?></td>
                        <td style="text-align:center;"><?= $v['paymentAmountTld']; ?></td>
                        <td style="text-align:center;"><?= $v['paymentTypeId']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>