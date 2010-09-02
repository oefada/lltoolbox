<?php $dailyRatesMap = array('Su', 'M', 'T', 'W', 'Th', 'F', 'S');
      //array(feeName => feeTypeId) -- 1 = percentage, 2 = dollar amount
      $feeDefaults = array('Taxes' => 1, 'Service Charges' => 1, 'Resort Fees' => 2);
      
      $newRate = false;
      $editItem = (isset($_GET['isNewItem']));
?>

<table class="room-night">
    <tr>
       <td width="100">
            <table class="room-nights-col1">
                <tr>
                    <td>
                        <table id="roomType" class="roomTypeDetails">
                            <?php foreach ($ratePeriod['LoaItems'] as $roomNum => $item): ?>
                                <tr class="room-type">
                                    <td width="135">Room Type</td>
                                    <td colspan="2">
                                       <input type="hidden" name="data[<?php echo $i; ?>][LoaItemRatePeriod][loaItemRatePeriodId]" value="<?php echo $item['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>" />
                                       <span class="roomTypeReadOnly"><?php echo $item['LoaItem']['itemName']; ?></span>
                                       <input type="hidden" class="roomTypeId" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId']; ?>][LoaItem][loaItemId]" value="<?php echo $item['LoaItem']['loaItemId']; ?>" />
                                       <input type="hidden" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId']; ?>][LoaItem][loaItemTypeId]" value="<?php echo $item['LoaItem']['loaItemTypeId']; ?>" />
                                       <?php if ($editItem): ?>
                                          <input type="hidden" name="data[<?php echo $i; ?>][isNewItem]" value="true" />
                                       <?php endif; ?>
                                    </td>
                               </tr>
                                <?php foreach($item['LoaItemRate'] as $j => $rateItem):
                                        if (empty($rateItem['LoaItemRate']['price'])) {
                                            $newRate = true;
                                        }
                                        else {
                                            $existingRate = true;
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="3">
                                                <?php $class = ($j > 0) ? ' class="dailyRates"' : ' class="rate1"'; ?>
                                                <table<?php echo $class; ?>>
                                                    <tr<?php echo $class; ?>>
                                                        <th colspan="3">
                                                            <?php
                                                                $daysArr = array();
                                                                $daysInput = array();
                                                                for ($k=0; $k<=6; $k++) {
                                                                    if ($rateItem['LoaItemRate']["w{$k}"] == 1 || !isset($isDailyRates)) {
                                                                        $daysArr[] = $dailyRatesMap[$k];
                                                                        $checked = ' checked="checked"';
                                                                    }
                                                                    else {
                                                                        $checked = '';
                                                                    }
                                                                    $style = (count($item['LoaItemRate']) == 1 || !$editItem) ? ' style="display:none"' : '';
                                                                    $disabled = ($editItem) ? '' : ' disabled="disabled"';
                                                                    $input = '<input type="checkbox" class="weekdaysInput w'.$k.'" weekday="w'.$k.'" name="data['.$i.'][LoaItems]['.$item['LoaItem']['loaItemId'].'][LoaItemRate]['.$j.'][w'.$k.']"'.$checked.$style.$disabled.' /> <span class="weekday-label">'.$dailyRatesMap[$k].'</span>';
                                                                    $daysInput[] = $input;
                                                                } ?>
                                                            <span id="rateLabel<?php echo $j; ?>">
                                                                <?php if ($item['LoaItem']['loaItemTypeId'] == 12): ?>
                                                                    Package Rate
                                                                <?php elseif (isset($isDailyRates)): ?>
                                                                    <?php echo $item['LoaItem']['itemName']; ?>: Rate <?php echo $j+1; ?>
                                                                <?php else: ?>
                                                                    Rate per Night
                                                                <?php endif; ?>
                                                            </span>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                        <td>Days Valid</td>
                                                        <td># Nights</td>
                                                    </tr>
                                                    <tr class="room-<?php echo $roomNum+1; ?> rate-<?php echo $j+1; ?> rate">
                                                        <td width="120">
                                                            <?php echo $package['Currency']['currencyCode']; ?> 
                                                            <?php if (empty($rateItem['LoaItemRate']['price'])): ?>
                                                                    <input type="text" size="5" class="weekdaysInput price" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId'];?>][LoaItemRate][<?php echo $j; ?>][price]" value="<?php echo $rateItem['LoaItemRate']['price']; ?>" />
                                                                    <input type="hidden" class="weekdaysInput" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId'];?>][LoaItemRate][<?php echo $j; ?>][isNew]" value="true" />
                                                            <?php else: ?>
                                                                    <span class="price"><?php echo $rateItem['LoaItemRate']['price']; ?></span>
                                                            <?php endif; ?>
                                                            <input type="hidden" class="weekdaysInput" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId'];?>][LoaItemRate][<?php echo $j; ?>][loaItemRateId]" value="<?php echo $rateItem['LoaItemRate']['loaItemRateId']; ?>" />
                                                            <input type="hidden" class="weekdaysInput" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId'];?>][LoaItemRate][<?php echo $j; ?>][loaItemRatePeriodId]" value="<?php echo $rateItem['LoaItemRate']['loaItemRatePeriodId']; ?>" />
                                                        </td>
                                                        <td width="250">
                                                            <?php if ($editItem || !isset($isDailyRates)): ?>
                                                                <?php echo implode('&nbsp;', $daysInput); ?>
                                                            <?php else: ?>
                                                                <?php echo implode('&nbsp;', $daysArr); ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($i == 0 || $editItem): ?>
                                                            <?php $numNights = (isset($rateItem['LoaItemRatePackageRel']['numNights']) && $rateItem['LoaItem']['loaItemTypeId'] == 1) ? $rateItem['LoaItemRatePackageRel']['numNights'] : $package['Package']['numNights']; ?>
                                                                    <?php if (count($item['LoaItemRate']) > 1): ?>
                                                                        <span class="numNights-rate<?php echo $j+1; ?> numNights" style="display:none"><?php echo $numNights; ?></span>
                                                                        <input type="text" size="5" class="weekdaysInput numNights" id="input-numNights-rate<?php echo $j+1; ?>" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId'];?>][LoaItemRate][<?php echo $j; ?>][LoaItemRatePackageRel][numNights]" value="<?php echo $rateItem['LoaItemRatePackageRel']['numNights']; ?>" />
                                                                    <?php else: ?>
                                                                        <span class="numNights-rate<?php echo $j+1; ?> numNights"><?php echo $numNights; ?></span>
                                                                        <input type="text" size="5" class="weekdaysInput numNights" id="input-numNights-rate<?php echo $j+1; ?>" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId'];?>][LoaItemRate][<?php echo $j; ?>][LoaItemRatePackageRel][numNights]" value="<?php echo $numNights; ?>" style="display:none" />
                                                                    <?php endif; ?>
                                                            <?php else: ?>
                                                                <?php $numNights = (isset($rateItem['LoaItemRatePackageRel']['numNights'])) ? $rateItem['LoaItemRatePackageRel']['numNights'] : $ratePeriods[0]['LoaItems'][$i]['LoaItemRate'][$j]['LoaItemRatePackageRel']['numNights']; ?>
                                                                <span class="numNights-rate<?php echo $j+1; ?> numNights"><?php echo $numNights; ?></span>
                                                                <input type="text" class="weekdaysInput numNights numNights-rate<?php echo $j+1; ?>" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId'];?>][LoaItemRate][<?php echo $j; ?>][LoaItemRatePackageRel][numNights]" value="<?php echo $numNights ?>" style="display:none" />
                                                            <?php endif; ?>
                                                            <input type="hidden" class="weekdaysInput" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId'];?>][LoaItemRate][<?php echo $j; ?>][LoaItemRatePackageRel][loaItemRatePackageRelId]" value="<?php echo $rateItem['LoaItemRatePackageRel']['loaItemRatePackageRelId']; ?>" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                <?php endforeach; ?>
                                <?php if (!isset($isDailyRates)): ?>
                                    <?php   $weekdayIndex = $j + 1;
                                            $daysInput = preg_replace('/\[LoaItemRate\]\[[0-9]\]/', '[LoaItemRate]['.$weekdayIndex.']', $daysInput);
                                            $daysInput = preg_replace('/ \/>/', ' disabled="true" />', $daysInput);
                                            $daysInput = preg_replace('/checked/', ' ', $daysInput);
                                            $daysInput = preg_replace('/style="display:none"/', '', $daysInput);
                                    ?>
                                    <tr style="display:none" class="weekdaysInput rate2">
                                        <td colspan="3">
                                            <table>
                                                <tr>
                                                    <th colspan="3">Daily Rate 2</th>
                                                </tr>
                                                <tr class="room-<?php echo $roomNum+1; ?> rate-2 rate">
                                                    <td width="120">
                                                        <?php echo $package['Currency']['currencyCode']; ?> <input type="text" size="5" class="weekdaysInput price" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId']; ?>][LoaItemRate][<?php echo $weekdayIndex; ?>][price]" value="" disabled="true" />
                                                        <input type="hidden" class="weekdaysInput" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId'];?>][LoaItemRate][<?php echo $j+1; ?>][isNew]" value="true" disabled="disabled" />
                                                        <input type="hidden" class="weekdaysInput" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId']; ?>][LoaItemRate][<?php echo $weekdayIndex; ?>][loaItemRatePeriodId]" value="<?php echo $item['LoaItemRate'][$j]['LoaItemRate']['loaItemRatePeriodId']; ?>" disabled="true" />
                                                    </td>
                                                    <td width="250">
                                                        <?php echo implode('&nbsp;', $daysInput); ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($i == 0 || $editItem): ?>
                                                            <span class="numNights-rate2 numNights" style="display:none">&nbsp;</span>
                                                            <input type="text" size="5" id="input-numNights-rate2" class="weekdaysInput numNights" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId']; ?>][LoaItemRate][<?php echo $weekdayIndex; ?>][LoaItemRatePackageRel][numNights]" value="" disabled="true" />
                                                        <?php else: ?>
                                                            <span class="numNights-rate2 numNights">&nbsp;</span>
                                                            <input type="hidden" class="weekdaysInput numNights-rate2 numNights" name="data[<?php echo $i; ?>][LoaItems][<?php echo $item['LoaItem']['loaItemId']; ?>][LoaItemRate][<?php echo $weekdayIndex; ?>][LoaItemRatePackageRel][numNights]" value="" disabled="true" />
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if ($editItem && !$existingRate): ?>
                                <tr>
                                    <td colspan="3" class="rateOption">
                                        <?php if (isset($isDailyRates)): ?>
                                            <span class="rateOption edit-link">(Switch to Rate per Night)</span>
                                        <?php else: ?>
                                            <span class="rateOption edit-link">(Switch to Daily Rates)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                       </table>
                       <table id="roomNightTaxes_<?php echo $i+1; ?>" class="roomNightTaxes">
                       <?php $colspan = ($editItem) ? 3 : 2; ?>
                       <tr>
                           <th colspan="<?php echo $colspan; ?>">Taxes/Fees</th>
                        </tr>
                       <?php $feeLabels = array_keys($feeDefaults); ?>
                       <?php for ($l=0; $l<=2; $l++): ?>
                            <?php
                                if (!empty($ratePeriod['Fees'][0])) {
                                    if ($l == 0 || $l == 1) {
                                        $emptyFee = array();
                                        if (!empty($ratePeriod['Fees'][$l]) && $ratePeriod['Fees'][$l]['Fee']['feeTypeId'] > 1) {
                                            $emptyFee[] = array('Fee' => array('feeTypeId' => 1));
                                            array_splice($ratePeriod['Fees'], $l, 0, $emptyFee);
                                        }
                                    }
                                }
                            ?>
                            <?php $feeName = (!isset($ratePeriod['Fees'][$l]) || empty($ratePeriod['Fees'][$l]['Fee']['feeName'])) ? $feeLabels[$l] : $ratePeriod['Fees'][$l]['Fee']['feeName']; ?>
                            <?php $feeValue = (!isset($ratePeriod['Fees'][$l]) || empty($ratePeriod['Fees'][$l]['Fee']['feePercent'])) ? '' : $ratePeriod['Fees'][$l]['Fee']['feePercent']; ?>
                            <?php $feeTypeId = (!isset($ratePeriod['Fees'][$l]) || empty($ratePeriod['Fees'][$l]['Fee']['feeTypeId'])) ? (($l == 0 || $l == 1) ? 1 : 2) : $ratePeriod['Fees'][$l]['Fee']['feeTypeId']; ?>
                            <?php if ($i == 0 && $editItem): ?>
                                    <tr>
                                        <td width="125"><input type="text" size="15" id="fee<?php echo $l; ?>Label" name="data[<?php echo $i; ?>][Fee][<?php echo $l; ?>][feeName]" value="<?php echo $feeName; ?>" /></td>
                                        <td width="125"><?php echo ($feeTypeId == 2) ? $package['Currency']['currencyCode'].' ' : ''; ?>
                                            <input type="text" size="5" id="fee-<?php echo $l; ?>" name="data[<?php echo $i; ?>][Fee][<?php echo $l; ?>][feePercent]" value="<?php echo $feeValue; ?>" />
                                            <?php echo ($feeTypeId == 0 || $feeTypeId == 1) ? '%' : ''; ?>
                                            <input type="hidden" id="feeTypeId-<?php echo $l; ?>" name="data[<?php echo $i; ?>][Fee][<?php echo $l; ?>][feeTypeId]" value="<?php echo $feeTypeId; ?>" />
                                            <?php if (isset($ratePeriod['Fees'][$l])): ?>
                                                <input type="hidden" name="data[<?php echo $i; ?>][Fee][<?php echo $l; ?>][feeId]" value="<?php echo (empty($ratePeriod['Fees'][$l]['Fee']['feeId'])) ? '' : $ratePeriod['Fees'][$l]['Fee']['feeId']; ?>" />
                                            <?php endif; ?>
                                        </td>
                                        <td width="50">
                                            <span class="x-remove" onclick="removeFee(this, <?php echo $l; ?>, <?php echo $package['Package']['numNights']; ?>);">[x]</span>
                                        </td>
                                    </tr>
                            <?php else: ?>
                                    <tr class="fee-<?php echo $l; ?>">
                                        <td width="135" align="right" class="fee-name-<?php echo $l; ?>"><?php echo $feeName; ?></td>
                                        <td>
                                            <?php echo ($feeTypeId == 2) ? $package['Currency']['currencyCode'].' ' : ''; ?><span id="fee-<?php echo $l; ?>"><?php echo $feeValue; ?></span><?php echo ($feeTypeId == 1) ? '%' : ''; ?>
                                            <span style="display:none" id="feeTypeId-<?php echo $l; ?>"><?php echo $feeTypeId; ?></span> 
                                        </td>
                                    </tr>
                            <?php endif; ?>
                        <?php endfor; ?>
                        </table>
                    </td>
                </tr>
            </table>
       </td>
       <td align="right">
            <table class="room-nights-col2">
                <?php if ($editItem): ?>
                   <?php foreach($ratePeriod['Validity'] as $index => $range): ?>
                        <?php echo $this->element('package/datepicker', array('range' => $range, 'i' => $i, 'index' => $index)); ?>
                   <?php endforeach; ?>
                   <tr>
                       <td colspan="2" align="right"><span class="link" onclick="addDateRange(<?php echo $ratePeriod['LoaItems'][0]['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>, <?php echo $i; ?>, <?php echo $index + 1; ?>, this)">Add Date Range</span></td>
                   </tr>
                <?php else: ?>
                  <th>Validities</th>
                   <?php foreach ($ratePeriod['Validity'] as $range): ?>
                      <tr class="validity">
                         <td><?php echo date('M j Y', strtotime($range['LoaItemDate']['startDate'])); ?> &#150; <?php echo date('M j Y', strtotime($range['LoaItemDate']['endDate'])); ?></td>
                      </tr>
                   <?php endforeach; ?>
                <?php endif; ?>
            </table>
       </td>
    </tr>
    <tr>
        <td colspan="2" class="total-price">
            Total Accommodations: <b><?php echo $package['Currency']['currencyCode']; ?> <span class="total-accommodations"><?php echo number_format($ratePeriod['Totals']['totalAccommodations'], 2); ?></span></b>
        </td>
    </tr>
</table>
