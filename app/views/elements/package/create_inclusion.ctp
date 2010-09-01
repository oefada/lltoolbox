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
        <span class="new-food-item" style="display:none">
            <div>
                PN: <input class="food" type="checkbox" onclick="javascript:perNightCheckbox(this);" name="data[<?php echo $i; ?>][LoaItem][<?php echo $j; ?>][PackageLoaItemRel][perNight]" disabled="true" />
            </div>
            <div>
                <input type="text" size="3" class="base-price" onblur="newFoodInclusionPrice(this)" name="data[<?php echo $i; ?>][LoaItem][<?php echo $j; ?>][itemBasePrice]" disabled="true"> <span class="per-night-multiplier"> x 1</span>
            </div>
        </span>
    </td>
    <td>
        <span class="total-price-input"><input type="text" size="3" name="data[<?php echo $i; ?>][LoaItem][<?php echo $j; ?>][itemBasePrice]"  /></span>
        <span class="total-price-readonly inclusion-price" style="display:none;"><?php echo $currencyCode; ?> <span class="total-price">0</span></span>
    </td>
    <td class="edit-link remove-inclusion">[x]</td>
</tr>