<tr class="new-inclusion">
    <td>
        <input type="text" size="40" name="data[<?php echo $i; ?>][LoaItem][<?php echo $j; ?>][itemName]" />
    </td>
    <td>
        <select class="new-loa-item-type" name="data[<?php echo $i; ?>][LoaItem][<?php echo $j; ?>][loaItemTypeId]">
            <option></option>
            <?php foreach($loaItemTypes as $itemType): ?>
                    <option value="<?php echo $itemType['LoaItemType']['loaItemTypeId']; ?>"><?php echo $itemType['LoaItemType']['loaItemTypeName']; ?></option>
            <?php endforeach; ?>
        </select>
    </td>
    <td>&nbsp;</td>
    <td class="per-night">&nbsp;
        <span class="new-item">
            <div>
                PN: <input class="inclusion-per-night" type="checkbox" onclick="javascript:perNightCheckbox(this, <?php echo $numNights; ?>);" name="data[<?php echo $i; ?>][LoaItem][<?php echo $j; ?>][PackageLoaItemRel][perNight]" />
                <input  type="hidden" name="data[<?php echo $i; ?>][LoaItem][<?php echo $j; ?>][PackageLoaItemRel][clientNumNights]" value="<?php echo $numNights; ?>" />
            </div>
            <div>
                <input type="text" size="3" class="base-price" onblur="newInclusionPrice(this)" name="data[<?php echo $i; ?>][LoaItem][<?php echo $j; ?>][itemBasePrice]"> <span class="per-night-multiplier"> x 1</span>
            </div>
        </span>
    </td>
    <td>
        <span class="total-price-readonly inclusion-price"><?php echo $currencyCode; ?> <span class="total-price">0</span></span>
    </td>
    <td class="edit-link remove-inclusion">[x]</td>
</tr>