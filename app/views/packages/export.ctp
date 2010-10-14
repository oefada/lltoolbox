<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <style>
        table#package-export {
            width:100%;
            border:1px solid silver;
            margin:auto;
        }
        table#package-export td,th {
            padding:3px;
            font-family: arial,verdana;
            font-size:13px;
            text-align:left;
        }
        table#package-export th {
            font-weight: bold;
            background-color: silver;
        }
        table#package-export td.m {
            text-align:center;
        }
        table#package-export td.hdr {
            font-size:18px;
            font-weight:bold;
            padding: 10px 0 10px 0;
        }
        table#package-export td.spacer {
            height: 25px;
        }
        table#package-export tr.bold td {
            font-weight: bold;
        }
        table#package-export td.bold {
            font-weight: bold;
        }
        table#package-export td.align-r {
            text-align: right;
        }
        table#package-export td.align-c {
            text-align: center;
        }
        table.lp {
            width:75%;
        }
        table.lp td {
            border-top: 1px solid silver;
        }
        .uline {
            text-decoration: underline;
        }
        .ital {
            font-style:italic;
        }
        </style>
        <!-- <pre><?=print_r($roomNights);?></pre>-->
    </head>
    <body>
        <table id="package-export" cellspacing="2" cellpadding="0">
            <tr><td colspan="4" class="m"><img src="http://www.luxurylink.com/images/ll_logo_2010.gif" /></td></tr>
            <tr><td colspan="4" class="m hdr uline"><?=strtoupper($client['name']);?></td></tr>
            <tr><td colspan="4" class="m"><?=$client['locationDisplay'];?><br /><a href="<?=$client['url'];?>"><?=$client['url'];?></a></td></tr>
            <tr><td colspan="4" class="spacer">&nbsp;</td></tr>
            <tr class="bold">
                <td width="15%" style="background-color:#b3ff85;">Package Level:</td>
                <td width="40%" style="background-color:#b3ff85;"><?=($package['Package']['isBarter']) ? 'Barter' : 'Remit';?></td>
                <td width="35%" style="background-color:#feffcb;">Client ID (internal use):</td>
                <td width="10%" style="background-color:#feffcb;"><?=$client['clientId'];?></td>
            </tr>
            <tr>
                <td>Date Created:</td>
                <td colspan="3"><?=$package['Package']['created'];?></td>
            </tr>
            <tr><td colspan="4" class="m hdr"><strong>LUXURY LINK SUGGESTED PACKAGE</strong></td></tr>
            <tr><td colspan="4" class="m ital">ALL PRICES IN <?=$package['Currency']['currencyName'];?></td></tr>
            <tr>
                <th>Working Title:</th>
                <td colspan="3" class="bold"><?=$package['Package']['packageName'];?></td>
            </tr>
            <tr>
                <th>Room Nights:</th>
                <td colspan="3"><?=$package['Package']['numNights'];?></td>
            </tr>
            <tr>
                <th>Number of Guests:</th>
                <td colspan="3"><?=$package['Package']['numGuests'];?></td>
            </tr>
            <tr><td colspan="4" class="spacer">&nbsp;</td></tr>
            
            <?php foreach ($roomNights as $rn):?>
            <tr>
                <td colspan="4" style="margin:0px;padding:15px 0 15px 0;border-top:1px solid silver;">
                    <table width="100%" >
                        <tr>
                            <th width="20%">Room Type:</th>
                            <td width="50%">
                                <?php foreach ($rn['LoaItems'] as $roomItem):?>
                                    <?=$roomItem['LoaItem']['itemName'];?><br />
                                <?php endforeach;?>
                            </td>
                            <td colspan="2">
                                <?php foreach ($rn['Validity'] as $v) { 
                                    echo '<strong>' . $v['LoaItemDate']['startDate'] . ' to ' . $v['LoaItemDate']['endDate'] . '</strong><br />';
                                }?>
                            </td>
                        </tr>
                        <tr>
                            <th>Rate Per Night</th>
                            <td colspan="3">
                                <?php if (count($rn['LoaItems'][0]['LoaItemRate']) > 1) :?>
                                    <?php foreach ($rn['LoaItems'][0]['LoaItemRate'] as $rateday) :?>
                                        <div><?=$rateday['LoaItemRate']['MultiDayPrice'];?> -- <?=$number->currency( $rateday['LoaItemRate']['price'], $cc);?></div>
                                    <?php endforeach;?>
                                <?php else :?>
                                    <?=$number->currency( $rn['LoaItems'][0]['LoaItemRate'][0]['LoaItemRate']['price'], $cc);?>
                                <?php endif;?>
                            </td>
                        </tr>
                        <?php foreach ($rn['Fees'] as $fees):?>
                        <tr>
                            <td><?=$fees['Fee']['feeName'];?></td>
                            <td colspan="3">
                                <?php if ($fees['Fee']['feeTypeId'] == 1) :?>
                                <?=$fees['Fee']['feePercent'];?>%
                                <?php else: ?>
                                <?=$number->currency( $fees['Fee']['feePercent'], $cc);?>
                                <?php endif;?>
                            </td>
                        </tr>
                        <?php endforeach;?>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <td align="right">Total Accommodations:</td>
                            <td align="right"><strong><?=$number->currency($rn['Totals']['totalAccommodations'], $cc);?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php endforeach;?>
            <tr><td colspan="4" class="spacer">&nbsp;</td></tr>
            <?php $alt = '';?>
            <tr>
                <th>Inclusions</th>
                <th>Name</th>
                <th>Price Per Night</th>
                <th>Total</th>
            </tr>
            <?php foreach ($loaItems as $ik => $item):?>
            <?php 
                $alt = ($alt ==	'') ? 'style="background-color:#f3f3f3;"' : '';
            ?>
            <tr>
                <td>&nbsp;</td>
                <td <?=$alt;?>>
                    <?=$item['LoaItem']['itemName'];?>
                    <?php if ($item['LoaItem']['loaItemTypeId'] == 12 && isset($item['LoaItem']['PackagedItems'])): ?>
                            <ul>
                            <?php foreach($item['LoaItem']['PackagedItems'] as $inclusion): ?>
                                    <?php if ($inclusion['LoaItem']['loaItemTypeId'] > 1): ?>
                                        <li><?php echo $inclusion['LoaItem']['itemName']; ?></li>
                                    <?php endif; ?>
                            <?php endforeach; ?>
                            </ul>
                    <?php endif; ?>
                </td>
                <td <?=$alt;?>><?=($item['PackageLoaItemRel']['quantity'] > 1) ? $number->currency($item['LoaItem']['totalPrice'], $cc) : '&nbsp;' ;?></td>
                <td <?=$alt;?>><strong><?=$number->currency( ($item['LoaItem']['totalPrice'] * $item['PackageLoaItemRel']['quantity']), $cc);?></strong></td>
            </tr>
            <?php endforeach;?>
            
            <tr><td colspan="4" class="spacer">&nbsp;</td></tr>
            <tr>
                <th>Valid for Travel</th>
                <td><?php foreach ($vb['ValidRanges'] as $vr) echo $vr . '<br />'?></td>
                <td colspan="2">&nbsp;</td>
            </tr>
            
            <tr><td colspan="4" class="spacer">&nbsp;</td></tr>
            <tr>
                <th>Blackout Dates</th>
                <td><?php foreach ($vb['BlackoutDays'] as $bo) echo $bo . '<br />'?></td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <?php if (!empty($bo_weekdays)): ?>
                <tr>
                    <th>Blackout Weekdays</th>
                    <td><?php echo $bo_weekdays; ?></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            <?php endif; ?>
            
            
            <tr><td colspan="4" class="spacer">&nbsp;</td></tr>
            <tr><td colspan="4" class="spacer">&nbsp;</td></tr>
            
            <?php foreach ($lowPrice as $lp) :?>
            <tr>
                <td colspan="4" class="align-r" style="padding:25px;border:1px solid #707070;background-color:#ececec;">
                    <table class="lp" cellspacing="0" cellpadding="10">
                    <tr>
                        <td class="bold align-r" valign="top" colspan="2">PACKAGE RETAIL VALUE:</td>
                        <td class="bold align-c" valign="top"><?=$lp['dateRanges'];?></td>
                        <td class="bold align-c" valign="top"><?=$number->currency( $lp['retailValue'], $cc);?></td>
                    </tr>
                    <tr class="starting-price">
                        <td class="bold align-r" valign="top" colspan="3" style="padding-top:15px;">LUXURY LINK STARTING PRICE</td>
                        <td class="bold align-c" valign="top" style="padding-top:15px;"><?=$number->currency( $lp['startPrice'], $cc);?></td>
                    </tr>
                    <tr class="starting-price">
                        <td class="align-r" valign="top" colspan="3">Percentage of Retail</td>
                        <td class="align-r" valign="top"><?=$lp['LoaItemRatePackageRel']['guaranteePercentRetail'];?>&nbsp;</td>
                    </tr>
                    </table>
                </td>
            </tr>
            <?php endforeach;?>
        </table>
        
        <script src="/js/jquery/jquery.js" type="text/javascript"></script>
        <script type="text/javascript"> //<![CDATA[
            $().ready(function() {
                   var hideStartingPrice = confirm('Do you want to hide the LL Starting Price for export?');
                   if (hideStartingPrice) {
                        $('tr.starting-price').hide();
                    }
                });
            //]]>
        </script>
        
    </body>
</html>