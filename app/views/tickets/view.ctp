<?php
    $this->pageTitle =
        $ticket['Package']['packageName']
        . ' '
        . $html2->c($ticket['Ticket']['ticketId'], 'Ticket Id:')
        . ' '
        . ' [' . $lltgServiceBuilder->getContext()->getLocaleCode() .']';
    $this->searchController = 'Tickets';
?>
<div class="tickets view">
<h2 class="title">Ticket Detail</h2>

<script type="text/javascript">
    /***
     * Script added by martin to allow for client notes
     */
    jQuery(function ($) {
        $(window).ready(function () {
            load_notes(<?= $ticket['Ticket']['ticketId']; ?>, 4);
        });
    });

</script>
<div id="noteModule" style="position: absolute; top: 194px; left: 940px;"></div>



<? if ($ticket['Ticket']['manualTicketInitials'] != '') {
    echo "<span style='color:red;'>Manual Ticket</span>";
} ?>
<? //print "<pre>";print_r($ticket);?>
<?php if (count($ticket['Client']) > 1) {
    echo "<h3>*** This is a MULTI-CLIENT package ***</h3>";
} ?>
<br/>

<div class="ticket-table">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td width="200"><strong>Ticket Id</strong></td>
    <td>
        <?php

        //hotel beds hack get clientid; if hotel beds, display HB
        $clientTicket = '';
        if ($ticket['Client']) {
            foreach ($ticket['Client'] as $client) {
                $clientTicket = $client['Client']['parentClientId'];
            }
        }

        if ($clientTicket == 11080) {
            echo $ticket['Ticket']['ticketId'] . 'HB';
        } else {
            echo $ticket['Ticket']['ticketId'];
        }
        ?>
    </td>
</tr>
<tr>
    <td width="200"><strong>TLD</strong></td>
    <td><strong><?php echo ($ticket['Ticket']['tldId'] == 2) ? '.CO.UK' : '.COM'; ?></strong></td>
</tr>
<tr>
    <td width="200"><strong>Site</strong></td>
    <td><strong><?php echo $siteIds[$ticket['Ticket']['siteId']]; ?></strong></td>
</tr>
<tr>
    <td width="200"><strong>Created</strong></td>
    <td><?php echo $ticket['Ticket']['created']; ?></td>
</tr>
<tr>
    <td width="200"><strong>Status</strong></td>
    <td><strong><?php echo $ticket['TicketStatus']['ticketStatusName']; ?></strong></td>
</tr>
<tr>
    <td width="200"><strong>Package Name</strong></td>
    <td><?php echo $ticket['Package']['packageName']; ?></td>
</tr>
<tr>
    <td><strong>Package Id</strong></td>
    <td><?php echo $html->link(
            $ticket['Ticket']['packageId'],
            array(
                'controller' => 'clients/' . $ticket['Client'][0]['Client']['clientId'],
                'action' => '/packages/summary/' . $ticket['Ticket']['packageId']
            )
        ); ?></td>
</tr>
<tr>
    <td width="200"><strong>Client(s)</strong></td>
    <td>
        <?php foreach ($ticket['Client'] as $client) : ?>
            <?php echo $html->link(
                $client['Client']['clientId'],
                array('controller' => 'clients', 'action' => 'edit', $client['Client']['clientId'])
            ); ?> -
            <?php echo $client['Client']['name']; ?><br/>
        <?php endforeach; ?>
    </td>
</tr>
<tr>
    <td><strong>Offer Id</strong></td>
    <td>
        <?php echo $html->link(
            $ticket['Ticket']['offerId'],
            array('controller' => 'reports', 'action' => 'offer_search', 'filter:' . urlencode($offer_search_serialize))
        ); ?>
        &nbsp;&nbsp;&nbsp;&nbsp;<a
            href="http://www.luxurylink.com/portfolio/por_offer_redirect.php?pid=<?php echo $ticket['Client'][0]['Client']['clientId']; ?>"
            target="_BLANK">Offer Page</a>
    </td>
</tr>
<tr>
    <td><strong>Offer Type</strong></td>
    <td><?php echo $offerType[$ticket['Ticket']['offerTypeId']]; ?></td>
</tr>
<tr>
    <td><strong>DNG Package</strong></td>
    <td><?php echo ($ticket['Package']['isDNGPackage'] == 1) ? 'Yes' : 'No'; ?></td>
</tr>
<tr>
    <td><strong>Ticket Amount</strong></td>
    <td><?php echo $number->currency($ticket['Ticket']['billingPrice']); ?></td>
</tr>
<?php if ($ticket['Ticket']['useTldCurrency'] == 1): ?>
<tr>
    <td><strong>Ticket Amount (Foreign Currency)</strong></td>
    <td><?php echo $number->currency($ticket['Ticket']['billingPriceTld'], $currencyName); ?></td>
</tr>
<?php endif; ?>
<tr>
    <td><strong>Processing Fee</strong></td>
    <td><?php echo $number->currency($processingFee, $currencyName); ?></td>
</tr>
<tr>
    <td><strong>User Id</strong></td>
    <td><?php echo $html->link(
            $ticket['Ticket']['userId'],
            array('controller' => 'users', 'action' => 'view', $ticket['Ticket']['userId'])
        ); ?></td>
</tr>
<tr>
    <td><strong>Name</strong></td>
    <td><?php echo $ticket['Ticket']['userFirstName'] . ' ' . $ticket['Ticket']['userLastName']; ?></td>
</tr>
<tr>
    <td><strong>Email</strong></td>
    <td><?php echo $ticket['Ticket']['userEmail1']; ?></td>
</tr>
<tr>
    <td><strong>Home Phone</strong></td>
    <td><?php echo $ticket['Ticket']['userHomePhone']; ?></td>
</tr>
<tr>
    <td><strong>Mobile Phone</strong></td>
    <td><?php echo $ticket['Ticket']['userMobilePhone']; ?></td>
</tr>
<tr>
    <td><strong>Work Phone</strong></td>
    <td><?php echo $ticket['Ticket']['userWorkPhone']; ?></td>
</tr>
<tr>
    <td><strong>Address</strong></td>
    <td>
        <?php
        if ($ticket['Ticket']['userAddress1']) {
            echo $ticket['Ticket']['userAddress1'];
        }
        if ($ticket['Ticket']['userAddress2']) {
            echo '<br />' . $ticket['Ticket']['userAddress2'];
        }
        if ($ticket['Ticket']['userAddress3']) {
            echo '<br/ >' . $ticket['Ticket']['userAddress3'];
        }
        echo '<br />' . $ticket['Ticket']['userCity'] . ', ' . $ticket['Ticket']['userState'] . ' ' . $ticket['Ticket']['userZip'] . '<br />' . $ticket['Ticket']['userCountry'];
        ?>
    </td>
</tr>


<? if ($preferDatesUser || $ticket['Ticket']['requestArrival'] || $ticket['Ticket']['requestArrival2']) { ?>
    <tr>
        <td><strong>Preferred Dates</strong></td>
        <td>
            <? if ($preferDatesUser) { ?>
                <? foreach ($preferDatesUser as $dt) { ?>
                    <div style="margin-bottom: 8px; line-height: 16px;">
                        <span style="font-weight: bold; margin-right: 10px;">Check In:</span> <?= date(
                            'm/d/Y (l M j)',
                            strtotime($dt['ReservationPreferDate']['arrivalDate'])
                        ); ?>
                        <br/>
                        <span style="font-weight: bold; margin-right: 10px;">Check Out:</span> <?= date(
                            'm/d/Y (l M j)',
                            strtotime($dt['ReservationPreferDate']['departureDate'])
                        ); ?>
                    </div>
                <? } ?>
            <? } else { ?>
                <? if ($ticket['Ticket']['requestArrival']) { ?>
                    <div style="margin-bottom: 8px; line-height: 16px;">
                        <span style="font-weight: bold; margin-right: 10px;">Check In:</span> <?= date(
                            'm/d/Y (l M j)',
                            strtotime($ticket['Ticket']['requestArrival'])
                        ); ?>
                        <br/>
                        <span style="font-weight: bold; margin-right: 10px;">Check Out:</span> <?= date(
                            'm/d/Y (l M j)',
                            strtotime($ticket['Ticket']['requestDeparture'])
                        ); ?>
                    </div>
                <? } ?>
                <? if ($ticket['Ticket']['requestArrival2'] && substr(
                        $ticket['Ticket']['requestArrival2'],
                        0,
                        4
                    ) != '0000'
                ) { ?>
                    <div style="margin-bottom: 8px; line-height: 16px;">
                        <span style="font-weight: bold; margin-right: 10px;">Check In:</span> <?= date(
                            'm/d/Y (l M j)',
                            strtotime($ticket['Ticket']['requestArrival2'])
                        ); ?>
                        <br/>
                        <span style="font-weight: bold; margin-right: 10px;">Check Out:</span> <?= date(
                            'm/d/Y (l M j)',
                            strtotime($ticket['Ticket']['requestDeparture2'])
                        ); ?>
                    </div>
                <? } ?>
            <? } ?>
        </td>
    </tr>
<? } ?>
<? if ($preferDatesHotel) { ?>
    <tr>
        <td><strong>Alternate Dates<br/>From Client</strong></td>
        <td>
            <? foreach ($preferDatesHotel as $dt) { ?>
                <div style="margin-bottom: 8px; line-height: 16px;">
                    <span style="font-weight: bold; margin-right: 10px;">Check In:</span> <?= date(
                        'm/d/Y (l M j)',
                        strtotime($dt['ReservationPreferDateFromHotel']['arrivalDate'])
                    ); ?>
                    <br/>
                    <span style="font-weight: bold; margin-right: 10px;">Check Out:</span> <?= date(
                        'm/d/Y (l M j)',
                        strtotime($dt['ReservationPreferDateFromHotel']['departureDate'])
                    ); ?>
                </div>
            <? } ?>
        </td>
    </tr>
<? } ?>



<?php if (isset($ticket['Ticket']['format']) && $ticket['Ticket']['format'] == 2) : ?>
    <tr>
        <td><strong>Request Num Guests</strong></td>
        <td><?php echo $ticket['Ticket']['requestNumGuests']; ?></td>
    </tr>
    <tr>
        <td><strong>Request Notes</strong></td>
        <td><?php echo $ticket['Ticket']['requestNotes']; ?></td>
    </tr>
<?php else: ?>
    <tr>
        <td><strong>Bid Id</strong></td>
        <td><?php echo $html->link(
                $ticket['Ticket']['bidId'],
                array('controller' => 'bids', 'action' => 'view', $ticket['Ticket']['bidId'])
            ); ?></td>
    </tr>
<?php endif; ?>
<tr>
    <td><strong>Ticket Notes</strong>
        <?php
        echo $html->link(
            'Edit',
            '/tickets/edit/' . $ticket['Ticket']['ticketId'],
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
        <?php if (!empty($ticket['Promo'])) : ?>
            <?php foreach ($ticket['Promo'] as $t_promo) : ?>

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
        <?php echo str_replace("\n", '<br/>', $ticket['Ticket']['ticketNotes']); ?></td>
</tr>
<tr>
    <td><strong>Call Logs</strong></td>
    <td>
        <?php $altRow = true;
        foreach ($ticket['Call'] as $call): $altRow = !$altRow; ?>
            <div class="<?php echo $altRow ? 'altRow' : '' ?> ticketCallLog">
                <div class="ticketCallLogNotes"><?php echo str_replace(
                        "\n",
                        "<br/>",
                        htmlentities($call['notes'])
                    ); ?></div>
                <div class="ticketCallLogInfo">
                    <div style="float: right;"><?php echo $call['representative']; ?></div>
                    Call #<?php echo $call['callId']; ?>, <?php echo $call['created']; ?></div>
            </div>
        <?php endforeach; ?>
    </td>
</tr>
</table>
</div>
<div style="clear:both;"></div>

<? if ($showEditLink) { ?>
    <a href="/tickets/updateDetails/<?= $ticket['Ticket']['ticketId']; ?>">Update Ticket Info</a>
<? } ?>

</div>

<br/>
<div class="collapsible">
    <div class="handle"><?php __('Payment Detail History (' . count($ticket['PaymentDetail']) . ')'); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (!empty($ticket['PaymentDetail'])): ?>
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th><?php __('Payment Detail Id'); ?></th>
                    <th><?php __('Payment Type'); ?></th>
                    <th><?php __('Processed Date'); ?></th>
                    <th><?php __('Billing Amount'); ?></th>
                    <th><?php __('Last Four CC'); ?></th>
                    <th><?php __('Processor'); ?></th>
                    <th><?php __('Status'); ?></th>
                    <th><?php __('CC Type'); ?></th>
                    <th><?php __('Initials'); ?></th>
                    <th class="actions"><?php __('Actions'); ?></th>
                </tr>
                <?php
                $i = 0;
                foreach ($ticket['PaymentDetail'] as $paymentDetail):
                    if (!isset($paymentDetail['isSuccessfulCharge'])) {
                        $processed_flag = "Payment Detail missing status of credit card charge. Please see a dev to prevent double charging customer.";
                    } else {
                        $processed_flag = $paymentDetail['isSuccessfulCharge'] ? 'Payment Successful' : 'Payment Declined';
                        if ($paymentDetail['isVoided'] == 1) {
                            $processed_flag = 'Payment Voided';
                        }
                    }
                    $class = null;
                    if ($i++ % 2 == 0) {
                        $class = ' class="altrow"';
                    }
                    ?>
                    <tr<?php echo $class; ?>>
                        <td align="center"><?php echo $paymentDetail['paymentDetailId']; ?></td>
                        <td align="center"><?php echo $paymentDetail['paymentTypeName']; ?></td>
                        <td align="center"><?php echo $paymentDetail['ppResponseDate']; ?></td>
                        <?php //$amount = isset($paymentDetail['ppBillingAmount']) && $paymentDetail['ppBillingAmount'] != 0 ? $paymentDetail['ppBillingAmount'] : $paymentDetail['paymentAmount']; ?>
                        <?php $amount = isset($ticket['Ticket']['useTldCurrency']) && $ticket['Ticket']['useTldCurrency'] != 1 ? $paymentDetail['paymentAmount'] : $paymentDetail['paymentAmountTld']; ?>
                        <td align="center"><?php echo $number->currency($amount, $currencyName); ?></td>
                        <td align="center"><?php echo $paymentDetail['ppCardNumLastFour']; ?></td>
                        <td align="center">
                            <?php
                            if (isset($paymentDetail['PaymentProcessor']['paymentProcessorName'])) {
                                echo $paymentDetail['PaymentProcessor']['paymentProcessorName'];
                            }
                            ?></td>
                        <td align="center">
                            <? if ($paymentDetail['paymentTypeId'] == 1) {
                                if ($paymentDetail['ppApprovalText'] == 'APPROVAL' && $paymentDetail['isSuccessfulCharge'] != 1) {
                                    echo "<span style='color:red;'>Please talk to a dev as the isSuccessfulCharge is not 1, but the approval text is 'APPROVAL'</span> ";
                                } else {
                                    echo $processed_flag;
                                }
                            } elseif ($paymentDetail['isVoided'] == 1) {
                                echo 'Payment Voided';
                            }
                            ?>
                        </td>
                        <td align="center"><?php echo $paymentDetail['ccType']; ?></td>
                        <td align="center"><?php echo $paymentDetail['initials']; ?></td>
                        <td class="actions">
                            <?php //echo $html->link(__('View', true), array('controller'=> 'payment_details', 'action'=>'view', $paymentDetail['paymentDetailId'])); ?>
                            <?php
                            echo $html->link(
                                'View',
                                '/payment_details/view/' . $paymentDetail['paymentDetailId'],
                                array(
                                    'title' => 'View Payment Transaction Details',
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
        <?php
        echo $html->link('Create New Payment', '/tickets/' . $ticket['Ticket']['ticketId'] . '/payment_details/add');
        if ($showVoidLink) {
            echo '<br /><br />';
            echo $html->link(
                'Void Existing Payment',
                '/tickets/' . $ticket['Ticket']['ticketId'] . '/payment_details/void'
            );
        }
        ?>

    </div>
</div>

<br/>
<div class="collapsible">
    <div class="handle"><?php __('Notifications and PPVs (' . count($ticket['PpvNotice']) . ')'); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (!empty($ticket['PpvNotice'])): ?>
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
                foreach ($ticket['PpvNotice'] as $ppvNotice):
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
                        <td><?php echo $ppvNotice['PpvNoticeType']['ppvNoticeTypeName']; ?>
                            (<?php echo $ppvNotice['PpvNoticeType']['ppvNoticeTypeId']; ?>)
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

        <div style="float:left;">
            <table width="340" cellspacing="3" cellpadding="3" border="0" style="border:0px;width:340px;">
                <tr>
                    <td style="border:0px;"><h2>Fixed Price</h2></td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Fixed Price - Winner Notification',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/12'
                        ); ?> - 12
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Fixed Price - Internal Exclusive Email',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/11'
                        ); ?> - 11
                    </td>
                </tr>
                <tr>
                    <?php if (count($ticket['Client']) > 1) : ?>
                        <td style="border:0px;">
                            ** Multi-Client Package **<br/>
                            <?php
                            foreach ($ticket['Client'] as $k => $client) {
                                echo $html->link(
                                    "Reservation Request - Client Exclusive Email [" . $client['Client']['name'] . "]",
                                    '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/10/' . $client['Client']['clientId']
                                );
                                echo ' - 10<br />';
                            }
                            ?>
                        </td>
                    <?php else : ?>
                        <td style="border:0px;"><?php echo $html->link(
                                'Reservation Request - Client Exclusive Email',
                                '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/10'
                            ); ?> - 10
                        </td>
                    <?php endif; ?>
                </tr>
            </table>
        </div>

        <div style="float:left;">
            <table width="340" cellspacing="3" cellpadding="3" border="0" style="border:0px;width:340px;">
                <tr>
                    <td style="border:0px;"><h2>Auctions</h2></td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Winner Email (PPV)',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/18'
                        ); ?> - 18
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Winner Email (Dec/Exp CC)',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/19'
                        ); ?> - 19
                    </td>
                </tr>
                <tr>
                    <?php if (count($ticket['Client']) > 1 && $ticket['Ticket']['siteId'] != 1) : ?>
                        <td style="border:0px;">
                            ** Multi-Client Package **<br/>
                            <?php
                            foreach ($ticket['Client'] as $k => $client) {
                                echo $html->link(
                                    "Client PPV [" . $client['Client']['name'] . "]",
                                    '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/4/' . $client['Client']['clientId']
                                );
                                echo ' - 4<br />';
                            }
                            ?>
                        </td>
                    <?php elseif ($ticket['Ticket']['siteId'] != 1) : ?>
                        <td style="border:0px;"><?php echo $html->link(
                                'Client PPV',
                                '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/4'
                            ); ?> - 4
                        </td>
                    <?php endif; ?>
                </tr>
                <? if ($ticket['Ticket']['siteId'] != 1) : ?>
                    <tr>
                        <td style="border:0px;"><?php echo $html->link(
                                'Chase Money',
                                '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/15'
                            ); ?> - 15
                        </td>
                    </tr>
                <? endif; ?>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            '1st Offense Flake',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/16'
                        ); ?> - 16
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            '2nd Offense Flake',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/17'
                        ); ?> - 17
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'No Dates Submitted (One Month Reminder)',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/52'
                        ); ?> - 52
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'New Dates Needed (One Week Reminder)',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/53'
                        ); ?> - 53
                    </td>
                </tr>
            </table>
        </div>

        <div style="float:left;">
            <table width="340" cellspacing="3" cellpadding="3" border="0" style="border:0px;width:340px;">
                <tr>
                    <td style="border:0px;"><h2>Reservations</h2></td>
                </tr>
                <tr>
                    <td style="border:0px;">
                        <?php echo $html->link(
                            'Reservation Request (No Special Requests)',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/2'
                        ); ?> - 2
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Confirmation',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/1'
                        ); ?> - 1
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Acknowledgement',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/12'
                        ); ?> - 12
                    </td>
                </tr>
                <? if ($ticket['Ticket']['siteId'] != 1) : ?>
                    <tr>
                        <td style="border:0px;"><?php echo $html->link(
                                'Reservation Dates Available',
                                '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/13'
                            ); ?> - 13
                        </td>
                    </tr>
                <? endif; ?>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Dates Not Available',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/14'
                        ); ?> - 14
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Request - Follow Up',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/24'
                        ); ?> - 24
                    </td>
                </tr>
                <? if ($ticket['Ticket']['siteId'] != 1) : ?>
                    <tr>
                        <td style="border:0px;"><?php echo $html->link(
                                'Reservation Request - Follow Up to Customer',
                                '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/32'
                            ); ?> - 32
                        </td>
                    </tr>
                <? endif; ?>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Cancellation - Request',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/29'
                        ); ?> - 29
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Cancellation - Confirmation',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/30'
                        ); ?> - 30
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Cancellation - Client Reciept',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/31'
                        ); ?> - 31
                    </td>
                </tr>
            </table>
        </div>

        <div style="float:left;">
            <table width="340" cellspacing="3" cellpadding="3" border="0" style="border:0px;width:340px;">
                <tr>
                    <td style="border:0px;"><h2>General Templates</h2></td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Customer Email Template',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/26'
                        ); ?> - 26
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Client Confirmation Template',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/27'
                        ); ?> - 27
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Change Dates Request Template',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/33'
                        ); ?> - 33
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Client Res Request Template',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/28'
                        ); ?> - 28
                    </td>
                </tr>
                <tr>
                    <td style="border:0px;"><?php echo $html->link(
                            'Reservation Request (Manual Auction Tickets)',
                            '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/54'
                        ); ?> - 54
                    </td>
                </tr>
            </table>
        </div>

        <div style="clear:both;"></div>
    </div>
</div>

<br/>
<div class="collapsible">
    <div class="handle"><?php __('Write-Off'); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (!empty($ticket['TicketWriteoff']['ticketWriteoffId'])): ?>
            <table cellpadding="0" cellspacing="0">
                <tr class="altrow">
                    <td width="200">Writeoff Id</td>
                    <td><?php echo $ticket['TicketWriteoff']['ticketWriteoffId']; ?></td>
                </tr>
                <tr>
                    <td width="200">Writeoff Reason</td>
                    <td><?php echo $ticket['TicketWriteoff']['TicketWriteoffReason']['ticketWriteoffReasonName']; ?></td>
                </tr>
                <tr class="altrow">
                    <td width="200">Writeoff Date</td>
                    <td><?php echo $ticket['TicketWriteoff']['dateRequested']; ?></td>
                </tr>
                <tr>
                    <td width="200">Writeoff Notes</td>
                    <td><?php echo $ticket['TicketWriteoff']['writeoffNotes']; ?></td>
                </tr>
            </table>
            <?php
            echo $html->link(
                'Edit Ticket Write-Off',
                '/ticket_writeoffs/edit/' . $ticket['TicketWriteoff']['ticketWriteoffId'],
                array(
                    'title' => 'Edit Ticket Write-Off',
                    'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                    'complete' => 'closeModalbox()'
                ),
                null,
                false
            );
            ?>
        <?php else: ?>
            <?php
            echo $html->link(
                'Write-Off this Ticket',
                '/tickets/' . $ticket['Ticket']['ticketId'] . '/ticket_writeoffs/add',
                array(
                    'title' => 'Ticket Write-Off',
                    'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                    'complete' => 'closeModalbox()'
                ),
                null,
                false
            );
            ?>
        <?php endif; ?>
    </div>
</div>

<? $refundOrCOFList = array('R' => 'Refund', 'C' => 'COF', 'B' => 'Both'); ?>

<br/>
<div class="collapsible">
    <div class="handle"><?php __("Refund Requests (" . sizeof($ticket['RefundRequest']) . ")"); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (sizeof($ticket['RefundRequest']) > 0): ?>
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <th style="text-align:center;">Request Id</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Request Date</th>
                    <th style="text-align:center;">Requested By</th>
                    <th style="text-align:center;">Approval Date</th>
                    <th style="text-align:center;">Approved By</th>
                    <th style="text-align:center;">Complete Date</th>
                    <th style="text-align:center;">Completed By</th>
                    <th style="text-align:center;">Refund / COF</th>
                    <th style="text-align:center;">&nbsp;</th>
                </tr>
                <?php foreach ($ticket['RefundRequest'] as $k => $v) : ?>
                    <tr>
                        <td style="text-align:center;"><?= $v['refundRequestId']; ?></td>
                        <td style="text-align:center;"><?= $v['RefundRequestStatus']['description']; ?></td>
                        <td style="text-align:center;"><?= $v['dateCreated']; ?></td>
                        <td style="text-align:center;"><?= $v['createdBy']; ?></td>
                        <td style="text-align:center;"><?= $v['dateApproved']; ?></td>
                        <td style="text-align:center;"><?= $v['approvedBy']; ?></td>
                        <td style="text-align:center;"><?= $v['dateCompleted']; ?></td>
                        <td style="text-align:center;"><?= $v['completedBy']; ?></td>
                        <td style="text-align:center;">
                            <?
                            if ($v['refundOrCOF']) {
                                echo $refundOrCOFList[$v['refundOrCOF']];
                            }
                            ?>
                        </td>
                        <td style="text-align:center;">
                            <?php echo $html->link('View', '/refund_requests/view/' . $v['refundRequestId']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        <?php echo $html->link('Add Refund Request', '/refund_requests/add/' . $ticket['Ticket']['ticketId']); ?>
    </div>
</div>

<br/>
<div class="collapsible">
    <? $refundProcessedCount = (empty($ticket['TicketRefund']['ticketRefundId'])) ? 0 : 1; ?>
    <div class="handle"><?php __('Refund Processed (' . $refundProcessedCount . ')'); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (!empty($ticket['TicketRefund']['ticketRefundId'])): ?>
            <table cellpadding="0" cellspacing="0">
                <tr class="altrow">
                    <td width="200">Refund Id</td>
                    <td><?php echo $ticket['TicketRefund']['ticketRefundId']; ?></td>
                </tr>
                <tr>
                    <td width="200">Refund Type</td>
                    <td><?php echo $ticket['TicketRefund']['TicketRefundType']['ticketRefundTypeName']; ?></td>
                </tr>
                <tr class="altrow">
                    <td width="200">Refund Reason</td>
                    <td><?php echo $ticket['TicketRefund']['RefundReason']['refundReasonName']; ?></td>
                </tr>
                <tr>
                    <td width="200">Refund Date</td>
                    <td><?php echo $ticket['TicketRefund']['dateRequested']; ?></td>
                </tr>
                <tr class="altrow">
                    <td width="200">Refund Amount</td>
                    <td><?php echo $number->currency($ticket['TicketRefund']['amountRefunded']); ?></td>
                </tr>
                <tr>
                    <td width="200">Refund Notes</td>
                    <td><?php echo $ticket['TicketRefund']['refundNotes']; ?></td>
                </tr>
            </table>

            <?php if ($showRefundLink): ?>
                <?php
                echo $html->link(
                    'Edit Ticket Refund',
                    '/ticket_refunds/edit/' . $ticket['TicketRefund']['ticketRefundId'],
                    array(
                        'title' => 'Edit Ticket Refund',
                        'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                        'complete' => 'closeModalbox()'
                    ),
                    null,
                    false
                );
                ?>
            <?php endif; ?>

        <?php else: ?>

            <?php if ($showRefundLink): ?>
                <?php
                echo $html->link(
                    'Refund this Ticket',
                    '/tickets/' . $ticket['Ticket']['ticketId'] . '/ticket_refunds/add',
                    array(
                        'title' => 'Ticket Refund',
                        'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                        'complete' => 'closeModalbox()'
                    ),
                    null,
                    false
                ); ?>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<br/>
<div class="collapsible">
    <div class="handle"><?php __("Revenue Allocation ($trackExistsCount)"); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if ($trackDetailExists): ?>
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <th style="text-align:center;">Track Detail Id</th>
                    <th style="text-align:center;">Ticket Id</th>
                    <th style="text-align:center;">Ticket Amount</th>
                    <th style="text-align:center;">Allocated Amount</th>
                    <th style="text-align:center;">Cycle</th>
                    <th style="text-align:center;">Iteration</th>
                    <th style="text-align:center;">Amount Kept</th>
                    <th style="text-align:center;">Amount Remitted</th>
                    <th style="text-align:center;">xy Running Total</th>
                    <th style="text-align:center;">xy Average</th>
                    <th style="text-align:center;">Keep Balance Due</th>
                    <th style="text-align:center;">Initials</th>
                </tr>
                <?php foreach ($trackDetails as $k => $v) : ?>
                    <?php $class_alt = ($v['trackDetail']['ticketId'] == $ticket['Ticket']['ticketId']) ? 'class="altrow"' : ''; ?>
                    <?php if (!$class_alt) {
                        continue;
                    } ?>
                    <tr <?php echo $class_alt; ?> >
                        <td style="text-align:center;"><?= $v['trackDetail']['trackDetailId']; ?></td>
                        <td style="text-align:center;"><?= $v['trackDetail']['ticketId']; ?></td>
                        <td style="text-align:center;"><?= $number->currency($v['trackDetail']['ticketAmount']); ?></td>
                        <td style="text-align:center;"><?= $number->currency(
                                $v['trackDetail']['allocatedAmount']
                            ); ?></td>
                        <td style="text-align:center;"><?= $v['trackDetail']['cycle']; ?></td>
                        <td style="text-align:center;"><?= $v['trackDetail']['iteration']; ?></td>
                        <td style="text-align:center;"><?= $number->currency($v['trackDetail']['amountKept']); ?></td>
                        <td style="text-align:center;"><?= $number->currency(
                                $v['trackDetail']['amountRemitted']
                            ); ?></td>
                        <td style="text-align:center;"><?= $number->currency(
                                $v['trackDetail']['xyRunningTotal']
                            ); ?></td>
                        <td style="text-align:center;"><?= $number->currency($v['trackDetail']['xyAverage']); ?></td>
                        <td style="text-align:center;"><?= $number->currency($v['trackDetail']['keepBalDue']); ?></td>
                        <td style="text-align:center;"><?= $v['trackDetail']['initials']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php echo $html->link(
                'View Track Detail',
                '/tickets/' . $ticket['Ticket']['ticketId'] . '/trackDetails/add'
            ); ?>
        <?php else: ?>
            <?php
            echo $html->link('Add Track Detail', '/tickets/' . $ticket['Ticket']['ticketId'] . '/trackDetails/add');
            /*
            echo $html->link('Add Track Detail',
                '/tickets/' . $ticket['Ticket']['ticketId'] . '/trackDetails/add',
                array(
                    'title' => 'Ticket - Track Detail Revenue Allocation',
                    'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
                    'complete' => 'closeModalbox()'
                    ),
                null,
                false
            );*/
            ?>
        <?php endif; ?>
    </div>
</div>

<br/>
<div class="collapsible">
    <?php
    $res_count = 0;
    if (isset($ticket['Reservation']['reservationId'])) {
        if ($ticket['Reservation']['reservationId'] > 0) {
            $res_count = 1;
        }
    }
    ?>
    <div class="handle"><?php __('Reservation Info (' . $res_count . ')'); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (!empty($ticket['Reservation']['reservationId'])): ?>
            <table cellpadding="0" cellspacing="0">
                <tr class="altrow">
                    <td width="200">Reservation Id</td>
                    <td><?php echo $ticket['Reservation']['reservationId']; ?></td>
                </tr>
                <tr>
                    <td width="200">Arrival Date</td>
                    <td><?php echo $ticket['Reservation']['arrivalDate']; ?></td>
                </tr>
                <tr class="altrow">
                    <td width="200">Departure Date</td>
                    <td><?php echo $ticket['Reservation']['departureDate']; ?></td>
                </tr>
                <tr>
                    <td width="200">Confirmation #</td>
                    <td><?php echo $ticket['Reservation']['reservationConfirmNum']; ?></td>
                </tr>
                <tr class="altrow">
                    <td width="200">Res. Conf. Sent to User</td>
                    <td>
                        <?php
                        if ($ticket['Reservation']['reservationConfirmToCustomer']) {
                            echo $ticket['Reservation']['reservationConfirmToCustomer'];
                        } else {
                            echo '<strong>No Email Sent to User Yet</strong><br />';
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <?php
            echo $html->link(
                'Edit Reservation',
                '/reservations/edit/' . $ticket['Reservation']['reservationId'],
                array(
                    'title' => 'Edit Reservation',
                    'onclick' => 'window.scrollTo(0,0);Modalbox.show(this.href, {title: this.title});return false',
                    'complete' => 'closeModalbox()'
                ),
                null,
                false
            );
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo '<a href="/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/1">Send Reservation Confirmation Email</a>'; ?>
        <?php else: ?>
            <?php
            echo $html->link(
                'Enter Reservation Information',
                '/tickets/' . $ticket['Ticket']['ticketId'] . '/reservations/add',
                array(
                    'title' => 'Enter Reservation Information',
                    'onclick' => 'window.scrollTo(0,0);Modalbox.show(this.href, {title: this.title});return false',
                    'complete' => 'closeModalbox()'
                ),
                null,
                false
            );
            ?>
        <?php endif; ?>
    </div>
</div>

<div class="collapsible">
    <?php
    $can_count = 0;
    if (isset($ticket['Cancellation']['cancellationId'])) {
        if ($ticket['Cancellation']['cancellationId'] > 0) {
            $can_count = 1;
        }
    }
    ?>
    <div class="handle"><?php __('Cancellation Info (' . $can_count . ')'); ?></div>
    <div class="collapsibleContent related">
        <br/>
        <?php if (!empty($ticket['Cancellation']['cancellationId'])): ?>
            <table cellpadding="0" cellspacing="0">
                <tr class="altrow">
                    <td width="200">Cancellation Id</td>
                    <td><?php echo $ticket['Cancellation']['cancellationId']; ?></td>
                </tr>
                <tr>
                    <td width="200">Cancellation #</td>
                    <td><?php echo $ticket['Cancellation']['cancellationNumber']; ?></td>
                </tr>
                <tr class="altrow">
                    <td width="200">Cancellation Note</td>
                    <td><?php echo $ticket['Cancellation']['cancellationNotes']; ?></td>
                </tr>
                <tr>
                    <td width="200">Confirmed By</td>
                    <td><?php echo $ticket['Cancellation']['confirmedBy']; ?></td>
                </tr>
                <tr>
                    <td width="200">Respond Recieved On</td>
                    <td><?php echo $ticket['Cancellation']['created']; ?></td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</div>

<br/>
<script>
    function setEndDate() {
        var id_s = "ReservationArrivalDate";
        var id_e = "ReservationDepartureDate";
        var ts_s = $(id_s).value.replace('-', '/').replace('-', '/');
        var ts_e = $(id_e).value.replace('-', '/').replace('-', '/');
        ts_s = Date.parse(ts_s);
        ts_e = Date.parse(ts_e);

        if (!ts_e || ts_e < ts_s) {
            var new_ts_e = ts_s + (86400000);
            var d = new Date();
            d.setTime(new_ts_e);
            var month = d.getMonth() + 1;
            var day = d.getDate();
            month = PadDigits(month, 2);
            day = PadDigits(day, 2);
            $(id_e).value = d.getFullYear() + '-' + month + '-' + day;
        }
    }
    function PadDigits(n, totalDigits) {
        n = n.toString();
        var pd = '';
        if (totalDigits > n.length) {
            for (i = 0; i < (totalDigits - n.length); i++) {
                pd += '0';
            }
        }
        return pd + n.toString();
    }
</script>
