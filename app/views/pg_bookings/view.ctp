<?php
    $this->pageTitle =
        $booking['Package']['packageName']
        . ' '
        . $html2->c($booking['PgBooking']['pgBookingId'], 'Ticket Id:')
        . ' '
        . ' [' . $lltgServiceBuilder->getContext()->getLocaleCode() .']';

?>
<div class="tickets view">
<h2 class="title">Pegasus Booking Detail</h2>

<script type="text/javascript">
    /***
     * Script added by martin to allow for client notes
     */
    jQuery(function ($) {
        $(window).ready(function () {
            load_notes(<?= $booking['PgBooking']['pgBookingId']; ?>, 4);
        });
    });

</script>
<div id="noteModule" style="position: absolute; top: 194px; left: 940px;"></div>


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
    <td width="200"><strong>Status</strong></td>
    <td>
        <?php
            if (isset($bookingStatusDisplay[$booking['PgBooking']['pgBookingStatusId']])) {
                echo $bookingStatusDisplay[$booking['PgBooking']['pgBookingStatusId']];
            }
        ?>
    </td>
</tr>
<? if ($booking['PgBooking']['pgBookingStatusId'] == 50) { ?>
    <tr>
        <td width="200"><strong>Cancellation Number</strong></td>
        <td><?php echo $booking['PgBooking']['cancellationNumber']; ?></td>
    </tr>
<? } ?>
<tr>
    <td width="200"><strong>Package Name</strong></td>
    <td><?php echo $booking['Package']['packageName']; ?></td>
</tr>
<tr>
    <td><strong>Package Id</strong></td>
    <td><?php echo $html->link(
            $booking['PgBooking']['packageId'],
            array(
                'controller' => 'clients/' . $booking['Client']['clientId'],
                'action' => '/packages/summary/' . $booking['PgBooking']['packageId']
            )
        ); ?></td>
</tr>

<tr>
    <td width="200"><strong>Client</strong></td>
    <td><?php echo $html->link(
            $booking['Client']['clientId'],
            array('controller' => 'clients', 'action' => 'edit', $booking['Client']['clientId'])
            ); ?> -
            <?php echo $booking['Client']['name']; ?><br/>
    </td>
</tr>
<tr>
    <td><strong>Ticket Amount</strong></td>
    <td><?php if ($booking['PgBooking']['tldId'] == 1) {
            echo $number->currency($booking['PgBooking']['subTotalUSD']);
         } else {
             echo $number->currency($booking['PgBooking']['subTotalUSD'], 'GBP');
         } ?>
    </td>
</tr>
<tr>
    <td><strong>Processing Fee</strong></td>
    <td><?php if ($booking['PgBooking']['tldId'] == 1) {
            echo $number->currency($booking['PgBooking']['handlingUSD']);
         } else {
             echo $number->currency($booking['PgBooking']['handlinglUSD'], 'GBP');
         } ?>
    </td>
</tr>
<tr>    <td width="200"><strong>User Id</strong></td>

		<td>
			<a href="/users/view/<?php echo $booking['User']['userId'];?>" target="_BLANK"><?php echo $booking['User']['userId'];?></a>
		</td>
</tr>
<tr>
    <td width="200"><strong>User Name</strong></td>
    <td><?php echo $booking['User']['firstName']; ?> <?php echo $booking['User']['lastName']; ?></td>
</tr>
<tr>
    <td><strong>User Email</strong></td>
    <td><?php echo $booking['User']['email']; ?></td>
</tr>
<tr>
    <td><strong>Home Phone</strong></td>
    <td><?php echo $booking['User']['homePhone']; ?></td>
</tr>
<tr>
    <td><strong>Mobile Phone</strong></td>
    <td><?php echo $booking['User']['mobilePhone']; ?></td>
</tr>
<tr>
    <td><strong>Work Phone</strong></td>
    <td><?php echo $booking['User']['workPhone']; ?></td>
</tr>
<tr>
    <td><strong>Address</strong></td>
    <td>
        <?php
        if ($booking['PgBooking']['billingAddress']) {
            echo $booking['PgBooking']['billingAddress'];
        }
        if ($booking['PgBooking']['billingAddress2']) {
            echo '<br />' . $booking['PgBooking']['billingAddress2'];
        }
        echo '<br />' . $booking['PgBooking']['billingCity'] . ', ' . $booking['PgBooking']['billingState'] . ' ' . $booking['PgBooking']['billingZip'] . '<br />' . $booking['PgBooking']['billingCountry'];
        ?>
    </td>
</tr>
<tr>
    <td width="200"><strong>Traveler</strong></td>
    <td><?php echo $booking['PgBooking']['travelerFirstName']; ?> <?php echo $booking['PgBooking']['travelerLastName']; ?></td>
</tr>
<tr>
    <td width="200"><strong>Hotel Confirmation Number</strong></td>
    <td><?php echo $booking['PgBooking']['confirmationNumber']; ?></td>
</tr>
<tr>
    <td width="200"><strong>Check-In</strong></td>
	<td><?php echo $booking['PgBooking']['dateIn'];?></td>
</tr>
<tr>
    <td width="200"><strong>Check-Out</strong></td>
	<td><?php echo $booking['PgBooking']['dateOut'];?></td>
</tr>
<tr>
    <td><strong>Ticket Notes</strong>
        <?php
        echo $html->link(
            'Edit',
            '/pg_bookings/edit/' . $booking['PgBooking']['pgBookingId'],
            array(
                'title' => 'Edit Ticket Notes',
                'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                'complete' => 'closeModalbox()'
            ),
            null,
            false
        );
        ?>
    </td>
    <td>
        <?php if (!empty($booking['Promo'])) : ?>
            <?php foreach ($booking['Promo'] as $t_promo) : ?>

                <h3 style="margin:0px;padding:0px;padding-bottom:5px;">** Promo Code
                    [<?= $t_promo['pc']['promoCode']; ?>] **</h3>

                <h3 style="margin:0px;padding:0px;padding-bottom:5px;">
                    <?php if ($t_promo['p']['amountOff']) : ?>
                        Amount Off: <?php echo $number->currency($t_promo['p']['amountOff']); ?>
                    <?php endif; ?>
                    <?php if ($t_promo['p']['percentOff']) : ?>
                        Percent Off: <?php echo $number->currency($t_promo['p']['percentOff']); ?>
                    <?php endif; ?>
                </h3>


            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($booking['Notes'])) : ?>
            <?php foreach ($booking['Notes'] as $t_note) : ?>
                <?= $t_note['notes']['note']; ?>
                (by <?= $t_note['notes']['userId']; ?>,
                <?= $t_note['notes']['dateCreated']; ?>)</br>
            <?php endforeach; ?>
        <?php endif; ?>
    </td>
</tr>
</table>
</div>
<div style="clear:both;"></div>

<?php if ($booking['PgBooking']['pgBookingStatusId'] == 1 || $booking['PgBooking']['pgBookingStatusId'] == 2): ?>
<br/><br/><br/>
<a href="/pg_bookings/cancel/<?php echo $booking['PgBooking']['pgBookingId']; ?>">Cancel This Booking</a>
<br/><br/><br/><br/>
<?php endif; ?>

<br/>
<div class="collapsible">
    <div class="handle"><?php __("Payments (" . sizeof($booking['PgPayment']) . ")"); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (sizeof($booking['PgPayment']) > 0): ?>
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <th style="text-align:center;">Payment Detail Id</th>
                    <th style="text-align:center;">Currency</th>
                    <th style="text-align:center;">Amount USD</th>
                    <th style="text-align:center;">Amount TLD</th>
                    <th style="text-align:center;">CC Type</th>
                </tr>
                <?php foreach ($booking['PgPayment'] as $k => $v) : ?>
                    <tr>
                        <td style="text-align:center;"><?= $v['pgPaymentId']; ?></td>
                        <td style="text-align:center;"><?= $v['currencyId']; ?></td>
                        <td style="text-align:center;"><?= $v['paymentUSD']; ?></td>
                        <td style="text-align:center;"><?= $v['paymentAmountTld']; ?></td>
                        <td style="text-align:center;">
                        <?php if ($v['paymentTypeId'] == 1) {
                                    echo "Credit Card";
                              }
                              if ($v['paymentTypeId'] == 2) {
                                    echo "Gift Certificate";
                              }
                              if ($v['paymentTypeId'] == 3) {
                                    echo "Credit on File";
                              }
                         ?>
                         </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>
<div class="collapsible">
    <div class="handle"><?php __('Notifications and PPVs (' . count($booking['PpvNotice']) . ')'); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (!empty($booking['PpvNotice'])): ?>
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th><?php __('XA ID'); ?></th>
                    <th><?php __('Notice Type'); ?></th>
                    <th><?php __('To'); ?></th>
                    <th><?php __('From'); ?></th>
                    <th><?php __('Subject'); ?></th>
                    <th><?php __('Sent'); ?></th>
                    <th><?php __('Initials'); ?></th>
                    <th class="actions"><?php __('Actions'); ?></th>
                </tr>
                <?php
                $i = 0;
                foreach ($booking['PpvNotice'] as $ppvNotice):
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }

                    if ($ppvNotice['emailBody'] && !$ppvNotice['emailBodyFileName']) {
                        $ppvNotice['emailTo'] = 'LEGACY';
                        $ppvNotice['emailTo'] = 'LEGACY';
                        $ppvNotice['emailFrom'] = 'LEGACY';
                        $ppvNotice['emailCc'] = 'LEGACY';
                        $ppvNotice['emailSubject'] = 'LEGACY';
                    }

                    ?>
                    <tr<?php echo $class; ?>>
                        <td><?php echo $ppvNotice['ppvNoticeId']; ?></td>
                        <td><?php echo $ppvNoticeTypes[$ppvNotice['ppvNoticeTypeId']]; ?>
                            (<?php echo $ppvNotice['ppvNoticeTypeId'] ?>)
                        </td>
                        <td><?php echo $ppvNotice['emailTo']; ?></td>
                        <td><?php echo $ppvNotice['emailFrom']; ?></td>
                        <td><?php echo $ppvNotice['emailSubject']; ?></td>
                        <td><?php echo $ppvNotice['emailSentDatetime']; ?></td>
                        <td><?php echo $ppvNotice['initials']; ?></td>
                        <td class="actions">
                            <?php
                            echo $html->link(
                                'View',
                                '/ppv_notices/view/' . $ppvNotice['ppvNoticeId'],
                                array(
                                    'title' => 'View PPV / Notice',
                                    'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                                    'complete' => 'closeModalbox()'
                                ),
                                null,
                                false
                            );
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <br/>




<!-- *** Comment out reservation PPV links until ticket 4869 gets slated - JW ***
        <div style="float:left;">
            <table width="340" cellspacing="3" cellpadding="3" border="0" style="border:0px;width:340px;">
                <tr>
                    <td style="border:0px;"><h2>Reservations</h2></td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Confirmation',
                            '/tickets/' . $booking['PgBooking']['pgBookingId'] . '/ppvNotices/add/1'
                        ); ?> - 1
                    </td>
                </tr>

                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Cancellation - Confirmation',
                            '/tickets/' . $booking['PgBooking']['pgBookingId'] . '/ppvNotices/add/30'
                        ); ?> - 30
                    </td>
                </tr>

            </table>
        </div>
		 -->


        <div style="clear:both;"></div>
    </div>
</div>
<div class="collapsible">
    <?php
    $res_count = 0;
    if (isset($booking['PgBooking']['confirmationNumber'])) {
        if ($booking['PgBooking']['confirmationNumber'] > 0) {
            $res_count = 1;
        }
    }
    ?>
    <div class="handle"><?php __('Reservation Info (' . $res_count . ')'); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (!empty($booking['PgBooking']['confirmationNumber'])): ?>
            <table cellpadding="0" cellspacing="0">
                <tr class="altrow">
                    <td width="200">Hotel Confirmation Number</td>
                    <td><?php echo $booking['PgBooking']['confirmationNumber']; ?></td>
                </tr>
                <tr>
                    <td width="200">Arrival Date</td>
                    <td><?php echo $booking['PgBooking']['dateIn']; ?></td>
                </tr>
                <tr class="altrow">
                    <td width="200">Departure Date</td>
                    <td><?php echo $booking['PgBooking']['dateOut']; ?></td>
                </tr>

            </table>
            <?php echo $html->link(
                              'Send Reservation Confirmation Email',
                              '/tickets/' . $booking['PgBooking']['pgBookingId'] . '/ppvNotices/add/1'
                              ); ?> - 1
        <?php endif; ?>
    </div>
</div>
