<div id="client-index">
<?php echo $ajax->form('selectAdditionalClient', 'post',
                     array('update' => 'client-index'));
?>
<?php echo $form->input('search', array('value' => $data['search'])); ?>
<?php echo $form->submit('Search');?>
</form>

<?php if ($showPagination) echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'client-index', 'showCount' => true)); ?>
<div class="clients index">
<table cellpadding="0" cellspacing="0">
<tr>
    <?php if ($showPagination): ?>
        <th><?php echo $paginator->sort('name');?></th>
        <th><?php echo $paginator->sort('Type', 'clientTypeId');?></th>
        <th><?php echo $paginator->sort('Level', 'clientLevelId');?></th>
        <th><?php echo $paginator->sort('Status','clientStatusId');?></th>
    <?php else: ?>
        <th>Name</th>
        <th>Type</th>
        <th>Level</th>
        <th>Status</th>
    <?php endif; ?>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($clients as $client):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<strong>
				<?php echo $client['Client']['name']; ?>
			</strong>		
		</td>
		<td>
			<?php echo $client['ClientType']['clientTypeName']; ?>
		</td>
		<td>
			<?php echo $client['ClientLevel']['clientLevelName']; ?>
		</td>
		<td>
			<?php //echo $client['ClientStatus']['clientStatusName']; ?>
		</td>
		<td class="actions">
			<?php echo $ajax->link(__('Select', true),
				array('action' => 'fetchMultipleClientsFormFragment', $client['Client']['clientId'], 'rowId' => $client['Client']['clientId']),
				array('update' => 'client_'.($client['Client']['clientId']),
					  'before' => '$("step1Fields").insert({bottom: "<div id=\"client_'.($client['Client']['clientId']).'\"></div>"})',
					  'indicator' => 'spinner',
					  'success' => 'Modalbox.hide(); $("firstPercentOfRevenue").setStyle({\'display\': \'block\'}); $("ClientLoaPackageRel0PercentOfRevenue").removeAttribute("disabled")'));
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php if ($showPagination) echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'client-index')); ?>
</div>
</div>
