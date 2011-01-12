<?php foreach ($packageClients as $client): ?>
    <?php if (count($packageClients) > 1): ?>
            <div class="combo-client-name"><?php echo $client['Client']['name']; ?></div>
    <?php endif; ?>
    <table id="available-inclusions-options">
        <tr>
            <th class="add-checkbox">Add to Package</th>
            <th width="400">LOA Item Name</th>
            <th>LOA Item Type</th>
            <th>Per-Night Item?</th>
            <th>Price</th>
        </tr>
        <?php foreach ($client['AvailableLoaItems'] as $availableInclusion): ?>
            <?php
                $class = ($i % 2 > 0) ? ' odd' : '';
                $i = (isset($i)) ? $i+1 : 0;
                $quantity = (isset($availableInclusion['PackageLoaItemRel']['quantity'])) ? $availableInclusion['PackageLoaItemRel']['quantity'] : 1; 
            ?>
            <tr class="item-type-<?php echo $availableInclusion['LoaItem']['loaItemTypeId']; ?><?php echo $class; ?>">
                <td class="add-checkbox"><input type="checkbox" name="data[<?php echo $i; ?>][AddInclusion][<?php echo $availableInclusion['LoaItem']['loaItemId']; ?>][loaItemId]" /></td>
                <td>
                    <?php if (in_array($availableInclusion['LoaItem']['loaItemTypeId'], array(12,13,14)) && !empty($availableInclusion['LoaItem']['PackagedItems'])): ?>
                        <b><?php echo $availableInclusion['LoaItem']['itemName']; ?></b>
                    <?php else: ?>
                        <?php echo $availableInclusion['LoaItem']['itemName']; ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $availableInclusion['LoaItemType']['loaItemTypeName']; ?></td>
                <td><input type="checkbox" name="data[<?php echo $i; ?>][AddInclusion][<?php echo $availableInclusion['LoaItem']['loaItemId']; ?>][perNight]" /></td>
                <td>
                    <?php echo $currencyCodes[$availableInclusion['LoaItem']['currencyId']]; ?> <?php echo round($availableInclusion['LoaItem']['totalPrice'], 2); ?></span>
                    <?php if ($availableInclusion['LoaItem']['totalPrice'] > $availableInclusion['LoaItem']['itemBasePrice']): ?>
                        <br />(Taxes Incl.)
                    <?php endif; ?>
                </td>
             </tr>
             <?php if (in_array($availableInclusion['LoaItem']['loaItemTypeId'], array(12,13,14)) && !empty($availableInclusion['LoaItem']['PackagedItems'])): ?>
                <?php foreach ($availableInclusion['LoaItem']['PackagedItems'] as $item): ?>
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
<?php endforeach; ?>