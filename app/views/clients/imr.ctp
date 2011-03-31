<?php
    $labelArray = array('Package Name',
                        'Price Point Name',
                        'Track Name',
                        'Offer Type',
                        'Site',
                        '# Nts',
                        'Pkg. Curr.',
                        'Retail',
                        'Price',
                        'Validity',
                        'Offer Performance',
                        'Conv. Rate',
                        'Start Date',
                        'End Date',
                        'Offer Status',
                        'Package Status');
?>
<link rel="stylesheet" type="text/css" href="/css/imr.css" />
<h2>Inventory Management Report</h2>
<div style="color:#000;padding-bottom:30px;">
    <div style="float:left;">Offers running <?php echo date('M j, Y', strtotime($searchStartDate)); ?> through <?php echo date('M j, Y', strtotime($searchEndDate)); ?></div>
    <div class="datepickers">
        <span>
              <form method="post">
                Start: <input type="text" size="12" class="datepicker startDate" name="data[searchStartDate]" value="<?php echo date('M j Y', strtotime($searchStartDate)); ?>" />
                End: <input type="text" size="12" class="datepicker endDate" name="data[searchEndDate]" value="<?php echo date('M j Y', strtotime($searchEndDate)); ?>" />
                <input type="submit" value="Search by Date" />
              </form>
        </span>
    </div>
</div>
<?php if (empty($schedulingMasters)): ?>
    <h3>No offers have been found for this date range.</h3>
<?php else: ?>
    <?php // need thead and tbody tags for tablesorter plugin ?>
    <table id="imr" class="index">
        <thead>
            <tr>
                <?php foreach($labelArray as $label): ?>
                    <th><?php echo $label; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($schedulingMasters as $i => $master): ?>
            <tr>
                <td><a href="/clients/<?php echo $clientId; ?>/packages/summary/<?php echo $master['SchedulingMaster']['packageId']; ?>"><?php echo $master['Package']['packageName']; ?></a></td>
                <td><?php echo $master['PricePoint']['name']; ?></td>
                <td><a href="/loas/edit/<?php echo $master['ClientLoaPackageRel']['loaId']; ?>"><?php echo $master['Track']['trackName']; ?></a></td>
                <td><?php echo $master['OfferType']['offerTypeName']; ?></td>
                <td align="center"><?php echo ($master['SchedulingMaster']['siteId'] == 1) ? 'LL' : 'FG'; ?></td>
                <td align="center"><?php echo $master['SchedulingMaster']['roomNights']; ?></td>
                <td align="center"><?php echo $master['Currency']['currencyCode']; ?></td>
                <td align="right" width="65"><?php echo $master['SchedulingMaster']['pricePointRetailValue']; ?></td>
                <td align="right" width="65" class="price"><?php echo $master['SchedulingMaster']['price']; ?></td>
                <td width="175"><?php if (!empty($master['SchedulingMaster']['validityDates'])): ?>
                                    <?php foreach($master['SchedulingMaster']['validityDates'] as $date): ?>
                                        <?php echo $date['LoaItemDate']['startDate']; ?> - <?php echo $date['LoaItemDate']['endDate']; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php if ($master['PricePoint']['name'] == 'Legacy'): ?>
                                        <?php echo $master['PricePoint']['validityStart']; ?> - <?php echo $master['PricePoint']['validityEnd']; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                </td>
                <td class="offers">
                    <?php if (!empty($master['Offers'])): ?>
                        <?php if ($master['SchedulingMaster']['offerTypeId'] == 4): ?>
                            <?php // link to ticket results for the scheduling master if there have been buy now requests ?>
                            <?php if ($master['Offers']['buyNowRequests'] > 0): ?>
                                <?php $searchStr = 's_offer_type_id:4' .
                                                    '/s_price_point_id:' . $master['SchedulingMaster']['pricePointId'] .
                                                    '/s_start_y:'. date('Y', strtotime($master['SchedulingMaster']['startDate'])) .
                                                    '/s_start_m:' . date('m', strtotime($master['SchedulingMaster']['startDate'])) .
                                                    '/s_start_d:' . date('d', strtotime($master['SchedulingMaster']['startDate'])) .
                                                    '/s_end_y:' . date('Y', strtotime($master['SchedulingMaster']['endDate'])) .
                                                    '/s_end_m:' . date('m', strtotime($master['SchedulingMaster']['endDate'])) .
                                                    '/s_end_d:' . date('d', strtotime($master['SchedulingMaster']['endDate']));
                                ?>
                                <a href="/tickets/index/<?php echo $searchStr; ?>">
                            <?php endif; ?>
                            <?php echo $master['Offers']['buyNowRequests']; ?> requests<?php if ($master['Offers']['buyNowRequests'] > 0): ?></a><?php endif; ?>, <?php echo $master['Offers']['buyNowConfirmedRequests']; ?> confirmed
                        <?php else: ?>
                            <?php foreach ($master['Offers'][$master['SchedulingMaster']['offerStatus']] as $offer): ?>
                                <span class="bids" id="offer-<?php echo $offer['Offer']['offerId']; ?>"><?php echo $offer['Offer']['bidCount']; ?></span>
                                <?php if ($offer['Offer']['bidCount'] > 0): ?>
                                    <span class="retail" style="display:none;"><?php echo $offer['Offer']['retailValue']; ?></span>
                                    <span class="winningBid" style="display:none;"><?php echo $offer['Offer']['winningBidAmount']; ?></span>
                                    <span class="endDate" style="display:none;"><?php echo $offer['Offer']['endDate']; ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if ($master['SchedulingMaster']['offerStatus'] == 'Live'): ?>
                                L 
                            <?php endif; ?>
                            <?php if ($master['Offers']['isScheduled'] == 1): ?>
                                S
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td align="right"><?php echo $master['Offers']['conversionRate']; ?>%</td>
                <td><?php echo $master['SchedulingMaster']['startDate']; ?></td>
                <td><?php echo $master['SchedulingMaster']['endDate']; ?></td>
                <td><?php echo $master['SchedulingMaster']['offerStatus']; ?></td>
                <td>
                    <?php //only link to scheduling if package has been approved for scheduling ?>
                    <?php if ($master['Package']['packageStatusId'] == 4): ?>
                        <a href="/scheduling/index/clientId:<?php echo $clientId; ?>">
                    <?php endif; ?>
                    <?php echo $master['PackageStatus']['packageStatusName']; ?>
                    <?php if ($master['Package']['packageStatusId'] == 4): ?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script type="text/javascript" src="/js/jquery/jquery.tablesorter.min.js"></script>
<?php endif; ?>
<script type="text/javascript" src="/js/imr.js"></script>