<?php
    $this->layout = 'overlay_form';
?>

<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>

<form method="post">
    <div class="section-title">Select Room Type(s) and Quantity</div>
    <br /><br />
    <?php foreach($package['ClientLoaPackageRel'] as $packageClient): ?>
        <?php if (count($package['ClientLoaPackageRel']) > 1): ?>
                <div class="combo-client-name"><?php echo $packageClient['Client']['name']; ?></div>
        <?php endif; ?>
        <table>
            <tr>
                <th>Add</th><th>&nbsp;</th><th>Room Type</th><th># Rooms</th>
            </tr>
            <?php foreach ($packageClient['ClientLoaPackageRel']['Rooms'] as $roomType): ?>
                <?php $checked = (!empty($roomType['LoaItem']['inPackage'])) ? 'checked' : ''; ?>
                <?php $quantity = (isset($roomType['PackageLoaItemRel']['quantity'])) ? $roomType['PackageLoaItemRel']['quantity'] : 1; ?>
                <tr>
                    <td width="20">
                        <input type="checkbox" name="data[LoaItem][<?php echo $roomType['LoaItem']['loaItemId']; ?>][checked]" <?php echo $checked; ?> />
                        <?php if (!empty($roomType['PackageLoaItemRel']['packageLoaItemRelId'])): ?>
                                <input type="hidden" name="data[LoaItem][<?php echo $roomType['LoaItem']['loaItemId']; ?>][PackageLoaItemRel][packageLoaItemRelId]" value="<?php echo $roomType['PackageLoaItemRel']['packageLoaItemRelId']; ?>" />
                        <?php endif; ?>
                    </td>
                    <td width="300">
                        <?php echo $roomType['LoaItem']['itemName']; ?>
                    </td>
                    <td>
                        <?php echo $roomType['LoaItemType']['loaItemTypeName']; ?>  
                    </td>
                    <td>
                        <input type="text" name="data[LoaItem][<?php echo $roomType['LoaItem']['loaItemId']; ?>][quantity]" size="5" value="<?php echo $quantity; ?>" />
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($package['ClientLoaPackageRel']) == 1): ?>
                <tr>
                    <td colspan="3">
                        <span class="edit-link" id="create-room-type">Create New Room Type</span>
                    </td>
                </tr>
            <?php endif; ?>
            <tr style="display:none;" class="add-room-type">
                <td width="20">
                    <input type="checkbox" disabled="true" checked />
                </td>
                <td width="400">
                    <input type="text" name="data[NewLoaItem][0][itemName]" size="45" disabled="true" />
                </td>
                <td>
                    <input type="text" size="5" name="data[NewLoaItem][0][quantity]" value="1" disabled="true" />
                </td>
            </tr>
        </table>
    <?php endforeach; ?>
    <input type="submit" value="Save and Continue to Rate Periods" />
</form>