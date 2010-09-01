<table id="available-inclusions-options">
    <tr>
        <th class="add-checkbox">Add to Package</th>
        <th width="400">LOA Item Name</th>
        <th>LOA Item Type</th>
        <th>Price</th>
    </tr>
    <?php foreach ($availableInclusions as $ai): ?>
        <?php
            $class = ($i % 2 > 0) ? ' odd' : '';
            $i = (isset($i)) ? $i+1 : 0;
            $quantity = (isset($ai['PackageLoaItemRel']['quantity'])) ? $ai['PackageLoaItemRel']['quantity'] : 1; 
        ?>
        <tr class="item-type-<?php echo $ai['LoaItem']['loaItemTypeId']; ?><?php echo $class; ?>">
            <td class="add-checkbox"><input type="checkbox" name="data[<?php echo $i; ?>][AddInclusion][<?php echo $ai['LoaItem']['loaItemId']; ?>]" /></td>
            <td>
                <?php if (in_array($ai['LoaItem']['loaItemTypeId'], array(12,13,14)) && !empty($ai['LoaItem']['PackagedItems'])): ?>
                    <b><?php echo $ai['LoaItem']['itemName']; ?></b>
                <?php else: ?>
                    <?php echo $ai['LoaItem']['itemName']; ?>
                <?php endif; ?>
            </td>
            <td><?php echo $ai['LoaItemType']['loaItemTypeName']; ?></td>
            <td>
                <?php echo $currencyCodes[$ai['LoaItem']['currencyId']]; ?> <?php echo round($ai['LoaItem']['totalPrice'], 2); ?></span>
                <?php if ($ai['LoaItem']['totalPrice'] > $ai['LoaItem']['itemBasePrice']): ?>
                    <br />(Taxes Incl.)
                <?php endif; ?>
            </td>
         </tr>
         <?php if (in_array($ai['LoaItem']['loaItemTypeId'], array(12,13,14)) && !empty($ai['LoaItem']['PackagedItems'])): ?>
            <?php foreach ($ai['LoaItem']['PackagedItems'] as $item): ?>
               <tr class="<?php echo $class; ?>">
                   <td>&nbsp;</td>
                   <td class="item-name prepackaged"><?php echo $item['LoaItem']['merchandisingDescription']; ?></td>
                   <td><?php echo $item['LoaItemType']['loaItemTypeName']; ?></td>
                   <td><?php echo $currencyCodes[$item['LoaItem']['currencyId']]; ?> 0</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</table>