<?php
    $this->layout = 'overlay_form'; 
?>

<script type="text/javascript">
    var clientId = <?php echo $clientId; ?>;
    var packageId = <?php echo $packageId; ?>;
    var numNights = <?php echo $numNights; ?>;
</script>

<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>

<div id="errorsContainer" style="display:none;">
    Please fix the following errors:<br />
    <ol>
        <div id="errors">&nbsp;</div>
    </ol>
</div>

<div class="section-title">Included in this Package</div>
<form id="inclusionsForm">
    <?php foreach ($package['ClientLoaPackageRel'] as $packageClient): ?>    
        <table class="inclusions-summary">
            <tr>
                <th width="400">
                    <?php if ($isMultiClientPackage): ?>
                        <div class="combo-client-name"><?php echo $packageClient['Client']['name']; ?></div>
                    <?php else: ?>
                        &nbsp;
                    <?php endif; ?>
                </th>
                <th>LOA Item Type</th>
                <th class="per-night">Price Per Night</th>
                <th>Total</th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="per-night">
                    <div>PN</div><div>Price</div>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <?php if (!empty($packageClient['roomLabel'])): ?>
                <tr class="odd">
                    <td class="item-name" colspan="5">
                        <?php echo $packageClient['roomLabel']; ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php $i = 0; ?>
            <?php foreach($packageClient['ExistingInclusions'] as $i => $inclusion): ?>
                <?php $class = ($i % 2 > 0) ? ' class="odd"' : ''; ?>
                <tr<?php echo $class; ?>>
                    <td class="item-name">
                        <?php if (in_array($inclusion['LoaItem']['loaItemTypeId'], array(12,13,14)) && !empty($inclusion['LoaItem']['PackagedItems'])): ?>
                                <?php echo $inclusion['LoaItem']['itemName']; ?>
                                <ul>
                                <?php foreach ($inclusion['LoaItem']['PackagedItems'] as $item): ?>
                                    <li><?php echo $item['LoaItem']['itemName']; ?></li>
                                <?php endforeach; ?>
                                </ul>
                        <?php else: ?>
                            <?php echo $inclusion['LoaItem']['itemName']; ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $inclusion['LoaItemType']['loaItemTypeName']; ?></td>
                    <td class="per-night">
                        <?php if ($inclusion['LoaItem']['loaItemTypeId'] == 5): ?>
                            <div>
                                <?php $checked = ($inclusion['PackageLoaItemRel']['quantity'] == $numNights) ? ' checked ' : ''; ?>
                                <input class="food" type="checkbox" onclick="javascript:perNightCheckbox(this);" name="data[<?php echo $i; ?>][PackageLoaItemRel][perNight]" <?php echo $checked; ?>/>
                                <input  type="hidden" name="data[<?php echo $i; ?>][PackageLoaItemRel][packageLoaItemRelId]" value="<?php echo $inclusion['PackageLoaItemRel']['packageLoaItemRelId']; ?>" />
                            </div>
                            <div><?php echo $currencyCodes[$inclusion['LoaItem']['currencyId']]; ?> <?php echo round($inclusion['LoaItem']['itemBasePrice'], 2); ?> <span class="per-night-multiplier"> x <?php echo $inclusion['PackageLoaItemRel']['quantity']; ?></span></div>
                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="inclusion-price">
                            <?php echo $currencyCodes[$inclusion['LoaItem']['currencyId']]; ?> <span class="total-price"><?php echo ($inclusion['LoaItem']['loaItemTypeId'] == 5) ? round($inclusion['LoaItem']['totalPrice'] * $inclusion['PackageLoaItemRel']['quantity'], 2) : round($inclusion['LoaItem']['totalPrice'], 2); ?></span>
                            <?php if ($inclusion['LoaItem']['totalPrice'] > $inclusion['LoaItem']['itemBasePrice']): ?>
                                <br />(Taxes Incl.)
                            <?php endif; ?>
                        </span>
                    </td>
                    <td class="edit-link remove-inclusion" id="<?php echo $inclusion['PackageLoaItemRel']['packageLoaItemRelId'] ?>">[x]</td>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
    <?php if (isset($taxLabel)): ?>
        <?php $class = (($i+1) % 2 > 0) ? ' class="odd"' : ''; ?>
            <tr<?php echo $class; ?>>
                <td colspan="5"><?php echo $taxLabel; ?></td>
            </tr>
    <?php endif; ?>
    
        <table class="add-inclusions">
            <tr>
                <td colspan="4"><div class="section-title">Additional LOA Items Available for Inclusion</div></td>
            </tr>
            <tr>
                <td colspan="4">
                    Filter by LOA Item Type:
                        <select id="new-inclusions-filter">
                            <option value="0">All</option>
                            <?php foreach($itemTypes as $type): ?>
                                    <option value="<?php echo $type['LoaItemType']['loaItemTypeId']; ?>"><?php echo $type['LoaItemType']['loaItemTypeName']; ?></option>
                            <?php endforeach; ?>
                        </select>
                </td>
            </tr>
            <tr>
                <td id="available-inclusions" colspan="4">
                    <?php echo $this->element('package/available_inclusions', array('packageClients' => $package['ClientLoaPackageRel'], 'i' => $i)); ?>
                </td>
            </tr>
        </table>
    <?php //temporarily disable adding new loa items from the package for multi-client packages
        if (count($package['ClientLoaPackageRel']) == 1): ?>
            <?php $i = (isset($i)) ? $i : 0; ?>
            <table class="inclusions-summary">
                <tr>
                    <td colspan="6"><div class="section-title">Create New LOA Item (Inclusion)</div></td>
                </tr>
                <tr id="create-inclusions-header" style="display:none">
                    <th>LOA Item Name</th>
                    <th>LOA Item Type</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Price</th>
                </tr>
                <tr id="create-inclusion-row">
                    <td colspan="6">
                        <input type="button" class="create-inclusion" id="<?php echo $i; ?>" value="Create New LOA Item" />
                    </td>
                </tr>
            </table>
    <?php endif; ?>
    <input type="button" value="Save Changes" onclick="submitForm('inclusionsForm');" />
</form>
