<div id="client-index">
	<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'client-index', 'showCount' => true)); ?>
<div class="clients index">
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('name');?></th>
	<th><?php echo $paginator->sort('Type', 'clientTypeId');?></th>
	<th><?php echo $paginator->sort('Level', 'clientLevelId');?></th>
	<th><?php echo $paginator->sort('Status','clientStatusId');?></th>
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
			<?php echo $client['ClientStatus']['clientStatusName']; ?>
		</td>
		<td class="actions">
			<?php echo $ajax->link(__('Select', true),
				array('action' => 'fetchMultipleClientsFormFragment', $client['Client']['clientId'], 'rowId' => $rowId+1),
				array('update' => 'client_'.($rowId+1),
					  'before' => '$("client_'.$rowId.'").insert({after: "<div id=\"client_'.($rowId+1).'\"></div>"})',
					  'indicator' => 'spinner',
					  'success' => 'Modalbox.hide(); $("firstPercentOfRevenue").setStyle({\'display\': \'block\'}); $("addLink_'.$rowId.'").remove(); $("ClientLoaPackageRel0PercentOfRevenue").removeAttribute("disabled")'));
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'client-index')); ?>
</div>
</div>