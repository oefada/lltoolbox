<?php if (isset($_GET['overlayForm'])) {
        $this->layout = 'overlay_form';
}
?>
<div class="loaItems form" style="width:100%;height:100%;overflow:auto;">
	<?php $session->flash(); ?>
    <?php echo $ajax->form('add', 'post', array('url' => "/loas/{$currentLoa}/loa_items/clone_items", 'update' => 'MB_content', 'model' => 'LoaItem', 'complete' => 'closeModalbox()')); ?>
    <!-- <form method="post" action="/loas/<?php //echo $currentLoa; ?>/loa_items/clone_items"> -->
    <table class="clone-items-options">
        <tr>
            <th>Select LOA</th>
            <td>
                <select name="data[LoaItem][loaId]">
                    <?php foreach($loas as $loa): ?>
                            <?php $selected = ($loa['Loa']['loaId'] == $currentLoa) ? ' selected' : ''; ?>
                            <option value="<?php echo $loa['Loa']['loaId']; ?>"<?php echo $selected; ?>>[<?php echo $loa['Loa']['loaId']; ?>] <?php echo date('M j, Y', strtotime($loa['Loa']['startDate'])); ?> to <?php echo date('M j, Y', strtotime($loa['Loa']['endDate'])); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Select Currency</th>
            <td>
                <select name="data[LoaItem][currencyId]">
                    <?php foreach($currencies as $currencyId => $currencyCode): ?>
                            <?php $selected = ($loa['Loa']['currencyId'] == $currencyId) ? ' selected' : ''; ?>
                            <option value="<?php echo $currencyId; ?>"<?php echo $selected; ?>><?php echo $currencyCode; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <table class="related loaItems clone-items">
        <tr>
            <th>Clone Item</th>
            <th>Type</th>
            <th>Name</th>
            <th>Live Site Description</th>
            <th>Price</th>
        </tr>
        <?php foreach ($loaItems as $i => $item): ?>
                <?php $class = ($i % 2 == 0) ? ' class="altrow"' : ''; ?>
                <tr<?php echo $class; ?>>
                    <td class="clone-me">
                        <input type="checkbox" name="data[CloneItems][<?php echo $item['LoaItem']['loaItemId']; ?>][LoaItem][loaItemId]" value="<?php echo $item['LoaItem']['loaItemId']; ?>" checked />
                    </td>
                    <td><?php echo $item['LoaItemType']['loaItemTypeName']; ?></td>
                    <td><?php echo $item['LoaItem']['itemName']; ?></td>
                    <td><?php echo $item['LoaItem']['merchandisingDescription']; ?></td>
                    <td class="price"><span class="currency-code"><?php echo $item['Currency']['currencyCode']; ?></span> <?php echo $item['LoaItem']['itemBasePrice']; ?></td>
                </tr>
        <?php endforeach; ?>
    </table>
    <div class="submit" style="text-align:right;"><input class="MB_focusable" value="Clone Selected LOA Items" type="submit" /></div>
    <!--</form>-->
</div>
<?php if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>"; ?>