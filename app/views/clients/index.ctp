<?php
$this->pageTitle = 'Clients';
if (isset($query)) {
	$html->addCrumb('Clients', '/clients');
	$html->addCrumb('search for '.$query);
} else {
	$html->addCrumb('Clients');
}
$this->set('hideSidebar', true);
?>

<div id="client-index">
	Tools: <a href="/accolades">Accolades</a> | <a href="/presses">Press/Reviews</a> | <a href="<?php echo $this->webroot; ?>clients/estara_import">eStara Importer</a>
	<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<div class="clients index">
<?php if (isset($inactive) && $inactive == 1): ?>
	<a href="/clients/index/query:<?= (isset($query) ? $query : "") ?>">Hide inactive Clients</a>
<?php else: ?>
	<a href="/clients/index/query:<?= (isset($query) ? $query : "") ?>?inactive=1">Show inactive Clients</a>
<?php endif; ?>

	<?php if (isset($query) && !empty($query)): ?>
		<div style="clear: both">
		<strong>Search Criteria:</strong> <?php echo $query; ?> 
		</div>
	<?php endif ?>
<script language="JavaScript">
	jQuery(document).ready(function($) {
		<?= isset($inactive) ? "jQuery('#search-form').append('<input type=\"hidden\" name=\"inactive\" value=\"".$inactive."\">');" : "" ?>
	});
</script>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('name');?></th>
	<th><?php echo $paginator->sort('Type', 'clientTypeId');?></th>
	<th><?php echo $paginator->sort('Level', 'clientLevelId');?></th>
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
			<?php if (isset($query)): ?>
				<?php echo $html->link(__($text->highlight($client['Client']['name'], $query),true), array('action'=>'edit', $client['Client']['clientId']),array('escape'=>false)); ?>
			<?php else: ?>
				<?php echo $html->link(__($client['Client']['name'], true), array('action'=>'edit', $client['Client']['clientId'])); ?>
			<?php endif ?>
			</strong>		</td>
		<td>
			<?php echo $client['ClientType']['clientTypeName']; ?>
		</td>
		<td>
			<?php echo $client['ClientLevel']['clientLevelName']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('action'=>'edit', $client['Client']['clientId'])); ?>
		</td>
	</tr>
	<? 
		$numChildren = isset($client['ChildClient'])?count($client['ChildClient']):0;
	if ($numChildren): ?>
	<tr<?php echo $class;?>>
		<td colspan=5 class='collapsible' style='padding: 0 10px; border: 0'>
				<h3 class='handle'>Show Children <?=$html2->c($numChildren)?></h3>
			<div class='collapsibleContent'>
		<table>
			<?foreach($client['ChildClient'] as $child):?>
			<tr<?php echo $class;?>>
				<td>
					<strong>
					<?php if (isset($query)): ?>
						<?php echo $text->highlight($child['name'], $query); ?>
					<?php else: ?>
						<?php echo $html->link($child['name'], array('action'=>'edit', $child['clientId'])); ?>
					<?php endif ?>
					</strong>
				</td>
			</tr>
		<? endforeach; ?>
		</table>
		</div>
		</td>
	</tr>
	<? endif; ?>
<?php endforeach; ?>
</table>
</div>
<?php echo $this->renderElement('ajax_paginator'); ?>
</div>
