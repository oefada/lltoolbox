<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']);
?>
<?php if(count($packages) > 0): ?>
<div id='packages-index' class="packages index">
<h2><?php __('View All Packages');?></h2>
<?= $this->renderElement('ajax_paginator', array('showCount' => true))?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('Package ID', 'Package.packageId', array('url' => array('clientId' => $clientId))); ?></th>
	<th><?php echo $paginator->sort('LOA ID', 'ClientLoaPackageRel.loaId', array('url' => array('clientId' => $clientId)));?></th>
	<th><?php echo $paginator->sort('Package Name', 'Package.packageName', array('url' => array('clientId' => $clientId)));?></th>
	<th><?php echo $paginator->sort('Package Status', 'Package.packageStatusId', array('url' => array('clientId' => $clientId)));?></th>
	<th><?php echo $paginator->sort('Created', 'Package.created', array('url' => array('clientId' => $clientId)));?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($packages as $package):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
        <td><?php
            if (isset($_GET['preview'])) {
                $packagePreviewChecked = in_array($package['Package']['packageId'], explode(',', $_GET['preview']));
            } else {
                $packagePreviewChecked = true;
            }
            ?>
            <input type="checkbox" class="previewPackage" name="previewPackage" value="<?php echo $package['Package']['packageId']; ?>" <?php echo $packagePreviewChecked?'checked="checked"':''; ?> />
            <?php echo $package['Package']['packageId']; ?>
        </td>
		<td>
			<?php echo $package['ClientLoaPackageRel']['loaId']; ?>
		</td>
		<td>
            <?php
            switch ($package['Package']['siteId']) {
                case 1:
                    echo $html->image('http://www.luxurylink.com/favicon.ico', array('alt'=>'Luxury Link', 'title'=>'Luxury Link'));
                    echo '&#160;';
                    break;
                case 2:
                    echo $html->image('http://www.familygetaway.com/favicon.ico', array('alt'=>'Family Getaway', 'title'=>'Family Getaway'));
                    echo '&#160;';
                    break;
            }
            ?>
			<?php echo $html->link($package['Package']['packageName'], "/clients/$clientId/packages/summary/{$package['Package']['packageId']}"); ?>
		</td>
		<td>
			<?php echo $packageStatusIds[$package['Package']['packageStatusId']]; ?>
		</td>
		<td>
			<?php echo $html2->date($package['Package']['created']); ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), "/clients/$clientId/packages/summary/{$package['Package']['packageId']}"); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
    <?=
    $html->link(
        '<span>Preview Checked Packages</span>',
        "#",
        array('target' => '_blank', 'class' => 'button previewCheckedPackages', 'style' => isset($_GET['preview'])?'color: red;':''),
        null,
        false
    ); ?>
    <?= $this->renderElement('ajax_paginator')?>
</div>
<?php else: ?>
	  <div class="blankBar">
	  <h1>
	    <?=$ajax->link("Add the first package for {$client['Client']['name']}", "/clients/$clientId/packages/add", array('update' => 'content-area', 'indicator' => 'loading')) ?>
	  </h1>
	  <p>Create, manage, and delete packages related to this client.</p>
	</div>

<?php endif; ?>

<script type="text/javascript">
    jQuery(function(){
        var $ = jQuery;
        $('a.button.previewCheckedPackages').click(function(e){
            var selected = [];
            $('input[type="checkbox"]:checked.previewPackage').each(function(){
                selected.push($(this).val());
            });;
            if (selected.length < 1) {
                e.preventDefault();
                alert('Please check at least one package checkbox');
            } else {
                var previewUrl = 'http://' + window.location.host.replace('-toolboxdev','-lldev');
                previewUrl += '/luxury-hotels/preview.html';
                previewUrl += '?preview=packages';
                previewUrl += '&clid=<?php echo $clientId; ?>';
                previewUrl += '&packageIds='+selected.join(',');
                $(this).attr('href', previewUrl);
            }
        });
    });
</script>
