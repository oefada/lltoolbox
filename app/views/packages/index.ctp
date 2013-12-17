<?php
$clientEmails = array();
if (isset($client['ClientContact'])) {
    foreach ($client['ClientContact'] as $cc) {
        if (isset($cc['clientContactTypeId']) && $cc['clientContactTypeId'] == 2 && isset($cc['name']) && isset($cc['emailAddress'])) {
            $clientEmails[] = $cc['name'] . ' <' . $cc['emailAddress'] . '>';
        }
    }
}
$clientEmails = implode('; ', $clientEmails);
$accountManagerEmail = (isset($client['Client']['managerUsername']) && $client['Client']['managerUsername']) ? $client['Client']['managerUsername'] . '@luxurylink.com' : '';

$this->pageTitle = $client['Client']['name'] . $html2->c(
        $client['Client']['clientId'],
        'Client Id:'
    ) . '<br />' . $html2->c('manager: ' . $client['Client']['managerUsername']);
?>
<?php if (count($packages) > 0): ?>
    <div id='packages-index' class="packages index">
        <h2><?php __('View All Packages'); ?></h2>
        <?= $this->renderElement('ajax_paginator', array('showCount' => true)) ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?php echo $paginator->sort(
                        'Package ID',
                        'Package.packageId',
                        array('url' => array('clientId' => $clientId))
                    ); ?></th>
                <th><?php echo $paginator->sort(
                        'LOA ID',
                        'ClientLoaPackageRel.loaId',
                        array('url' => array('clientId' => $clientId))
                    ); ?></th>
                <th><?php echo $paginator->sort(
                        'Package Name',
                        'Package.packageName',
                        array('url' => array('clientId' => $clientId))
                    ); ?></th>
                <th><?php echo $paginator->sort(
                        'Package Status',
                        'Package.packageStatusId',
                        array('url' => array('clientId' => $clientId))
                    ); ?></th>
                <th><?php echo $paginator->sort(
                        'Created',
                        'Package.created',
                        array('url' => array('clientId' => $clientId))
                    ); ?></th>
                <th class="actions"><?php __('Actions'); ?></th>
                <th>PP Validity Ends</th>
            </tr>
            <?php
            $i = 0;
            foreach ($packages as $package):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
                ?>
                <tr<?php echo $class; ?>>
                    <td><?php echo $package['Package']['packageId']; ?></td>
                    <td>
                        <?php echo $package['ClientLoaPackageRel']['loaId'] ? $html->link($package['ClientLoaPackageRel']['loaId'], array('controller'=>'loas', 'action'=>'view', $package['ClientLoaPackageRel']['loaId'])) : '&#160;'; ?>
                    </td>
                    <td>

                        <?php echo $html->link(
                            $package['Package']['packageName'],
                            "/clients/$clientId/packages/summary/{$package['Package']['packageId']}"
                        ); ?>
                        <?php
                        if($package['Package']['isFamily']){
                            echo $html->image(
                                $this->webroot.'img/fam_tag24x19.png',
                                array('alt' => 'Family package', 'title' => 'Family package'));
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo $packageStatusIds[$package['Package']['packageStatusId']]; ?>
                    </td>
                    <td>
                        <?php echo $html2->date($package['Package']['created']); ?>
                    </td>
                    <td class="actions">
                        <?php echo $html->link(
                            __('View Details', true),
                            "/clients/$clientId/packages/summary/{$package['Package']['packageId']}"
                        ); ?>
                    </td>
                    <td>
                        <?php
                        $hideCheckbox = false;
                        $pppEnd = isset($package['Package']['lastPricePointValidityEnd']) ? $package['Package']['lastPricePointValidityEnd'] : '';
                        if (isset($_GET['preview'])) {
                            $packagePreviewChecked = in_array(
                                $package['Package']['packageId'],
                                explode(',', $_GET['preview'])
                            );
                        } else {
                            $packagePreviewChecked = date('Y-m-d') <= $pppEnd;
                        }
                        if ($package['Package']['siteId'] != 1) {
                            // LL ONLY!
                            $hideCheckbox = true;
                            $packagePreviewChecked = false;
                        }
                        ?>
                        <input
                            type="checkbox"
                            class="previewPackage"
                            name="previewPackage"
                            value="<?php echo $package['Package']['packageId']; ?>"
                            <?php echo $packagePreviewChecked ? 'checked="checked"' : ''; ?>
                            <?= $hideCheckbox ? 'disabled="disabled"' : ''; ?>
                            style="<?= $hideCheckbox ? 'opacity: 0.0' : ''; ?>"
                            />
                        <span <?php echo !$packagePreviewChecked ? 'style="opacity:0.5;"' : ''; ?>><?php echo $pppEnd; ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php    if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            } ?>
            <tr <?= $class; ?>>
                <td colspan="6" rowspan="4">&nbsp;</td>
                <td>
                    <?=
                    $html->link(
                        '<span>Preview</span>',
                        "#",
                        array(
                            'target' => '_blank',
                            'class' => 'button previewCheckedPackages',
                            'style' => isset($_GET['preview']) ? 'color: red;' : ''
                        ),
                        null,
                        false
                    ); ?>
                </td>
            </tr>
            <tr <?= $class; ?>>
                <td>
                    <hr/>
                    <br/>
                    <input type="checkbox" class="emailWho" name="emailWho" value="accountmanager" checked="checked "/>
                    Email AM
                </td>
            </tr>
            <tr <?= $class; ?>>
                <td>
                    <input type="checkbox" class="emailWho" name="emailWho" value="clients"/>
                    Email Client
                </td>
            </tr>
            <tr <?= $class; ?>>
                <td>
                    <?=
                    $html->link(
                        '<span>Create Email</span>',
                        "#",
                        array(
                            'class' => 'button emailCheckedPackages'
                        ),
                        null,
                        false
                    ); ?>
                </td>
            </tr>
        </table>
        <?= $this->renderElement('ajax_paginator') ?>
    </div>
<?php else: ?>
    <div class="blankBar">
        <h1>
            <?=
            $ajax->link(
                "Add the first package for {$client['Client']['name']}",
                "/clients/$clientId/packages/add",
                array('update' => 'content-area', 'indicator' => 'loading')
            ) ?>
        </h1>

        <p>Create, manage, and delete packages related to this client.</p>
    </div>

<?php endif; ?>

<script type="text/javascript">
    jQuery(function () {
        var $ = jQuery;
        var getCheckedPreviewUrl = function () {
            var selected = [];
            $('input[type="checkbox"]:checked.previewPackage').each(function () {
                selected.push($(this).val());
            });
            if (selected.length < 1) {
                return false;
            }
            // previewUrl test: http://jsfiddle.net/mK6Um/7/
            var previewUrl = 'http://' + window.location.host.replace(/toolboxdev/, 'lldev').replace(/-toolbox/, '-luxurylink').replace(/^toolbox/, 'www').replace(/(|\.luxurylink\.com)$/, '.luxurylink.com');
            previewUrl += '/luxury-hotels/preview.html';
            previewUrl += '?preview=package';
            previewUrl += '&clid=<?php echo $clientId; ?>';
            previewUrl += '&multiPackages=' + selected.join(',');
            return previewUrl;
        };
        $('a.button.previewCheckedPackages').click(function (e) {
            var url = getCheckedPreviewUrl();
            if (!url) {
                e.preventDefault();
                alert('Please check at least one package checkbox');
            } else {
                $(this).attr('href', url);
            }
        });
        $('a.button.emailCheckedPackages').click(function (e) {
            var url = getCheckedPreviewUrl();
            if (!url) {
                e.preventDefault();
                alert('Please check at least one package checkbox');
            } else {
                var mailurl = 'mailto:';
                var destEmails = '';
                $('input[type="checkbox"]:checked.emailWho').each(function () {
                    if (destEmails.length > 0) {
                        destEmails += ';';
                    }
                    switch ($(this).val()) {
                        case 'clients':
                            destEmails += '<?= $clientEmails;?>';
                            break;
                        case 'accountmanager':
                            destEmails += '<?= $accountManagerEmail; ?>';
                            break;
                    }
                });
                mailurl += encodeURIComponent(destEmails);
                mailurl += '?subject=' + encodeURIComponent("Package Preview for  <?= $client['Client']['name'];?>");
                mailurl += '&body=' + encodeURIComponent("\n\n" + url);
                $(this).attr('href', mailurl);
            }
        });
    });
</script>
