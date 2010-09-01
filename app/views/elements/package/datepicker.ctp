<tr class="validity">
    <td><input type="text" size="12" class="datepicker startDate" name="data[<?php echo $i; ?>][LoaItemDate][<?php echo $index; ?>][startDate]" value="<?php echo (!empty($range['LoaItemDate']['startDate'])) ? date('M j Y', strtotime($range['LoaItemDate']['startDate'])) : ''; ?>" />
        <input type="text" size="12" class="datepicker endDate" name="data[<?php echo $i; ?>][LoaItemDate][<?php echo $index; ?>][endDate]" value="<?php echo (!empty($range['LoaItemDate']['endDate'])) ? date('M j Y', strtotime($range['LoaItemDate']['endDate'])) : ''; ?>" />
        <input type="hidden" name="data[<?php echo $i; ?>][LoaItemDate][<?php echo $index; ?>][loaItemRatePeriodId]" value="<?php echo $range['LoaItemDate']['loaItemRatePeriodId']; ?>" />
        <input type="hidden" name="data[<?php echo $i; ?>][LoaItemDate][<?php echo $index; ?>][loaItemDateId]" value="<?php echo $range['LoaItemDate']['loaItemDateId']; ?>" />
    </td>
    <td><span class="x-remove" onclick="removeDateRange(this);">[x]</span></td>
</tr>