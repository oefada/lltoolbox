<div class="tickets view">
<h2 class="title">Cancel Pegasus Booking</h2>

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
    <td width="200"><strong>Confirmation Number</strong></td>
    <td><?php echo $booking['PgBooking']['confirmationNumber']; ?></td>
</tr>
<tr>
    <td width="200"><strong>Status</strong></td>
    <td><?php echo $booking['PgBooking']['pgBookingStatusId']; ?></td>
</tr>
</table>
</div>
<div style="clear:both;"></div>

<?php if ($booking['PgBooking']['pgBookingStatusId'] == 1): ?>
<br/><br/><br/>
<a href="/pg_bookings/cancel/<?php echo $booking['PgBooking']['pgBookingId']; ?>?confirm=<?php echo $booking['PgBooking']['pgBookingId']; ?>">Please Click Here To Confirm Cancellation</a>
<br/><br/><br/><br/>
<?php endif; ?>

