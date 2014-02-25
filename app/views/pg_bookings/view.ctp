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
</table>
</div>
<div style="clear:both;"></div>

