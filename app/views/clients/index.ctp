<div class="clients index">
<h2><?php __('Clients');?></h2>
<?php /* File: /app/views/people/index.ctp */?>
<form accept-charset="UNKNOWN" enctype="application/x-www-form-urlencoded" method="get">
<input id="query" maxlength="2147483647" name="query" size="20" type="text" />
<div id="loading" style="display: none; "><!--p echo $html--> image("spinner.gif") ?></div>
</form>
 
<?php
$options = array(
	'update' => 'view',
	'url'    => '/clients/search',
	'frequency' => 1,Å
	'loading' => "Element.hide('view');Element.show('loading')",
	'complete' => "Element.hide('loading');Effect.Appear('view')"
);
 
print $ajax -> observeField('query', $options);
?>
<div id="view" class="auto_complete">
	<!-- Results will load here --></div>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('clientId');?></th>
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
			<?php echo $client['Client']['clientId']; ?>
		</td>
		<td>
			<strong><?php echo $client['Client']['name']; ?></strong>
			<?php if (!empty($client['Client']['companyName'])): ?>
				<span class="italicize lightBlackTextSmall"><span class="grayTextSmall"> (</span><?=$client['Client']['companyName']?><span class="grayTextSmall">)</span></span>
			<?php endif ?>
			<div class="grayTextSmall">
				<?php if ($client['Client']['email1']): ?>
					<?php echo $client['Client']['email1']; ?><br />
				<?php endif ?>
				<?php if ($client['Client']['phone1']): ?>
					<?php echo $client['Client']['phone1']; ?><br />
				<?php endif ?>
				<?php if ($client['Client']['phone2']): ?>
					<?php echo $client['Client']['phone2']; ?><br />
				<?php endif ?>
			</div>
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
			<?php echo $html->link(__('View', true), array('action'=>'view', $client['Client']['clientId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $client['Client']['clientId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $client['Client']['clientId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $client['Client']['clientId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Client', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Client Levels', true), array('controller'=> 'client_levels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Level', true), array('controller'=> 'client_levels', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Statuses', true), array('controller'=> 'client_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Status', true), array('controller'=> 'client_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Types', true), array('controller'=> 'client_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Type', true), array('controller'=> 'client_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Regions', true), array('controller'=> 'regions', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Region', true), array('controller'=> 'regions', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Acquisition Sources', true), array('controller'=> 'client_acquisition_sources', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Acquisition Source', true), array('controller'=> 'client_acquisition_sources', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Loas', true), array('controller'=> 'loas', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa', true), array('controller'=> 'loas', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Addresses', true), array('controller'=> 'addresses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Address', true), array('controller'=> 'addresses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Theme Rels', true), array('controller'=> 'client_theme_rels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Theme Rel', true), array('controller'=> 'client_theme_rels', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Accolades', true), array('controller'=> 'accolades', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Accolade', true), array('controller'=> 'accolades', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Users', true), array('controller'=> 'users', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller'=> 'users', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Amenities', true), array('controller'=> 'amenities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Amenity', true), array('controller'=> 'amenities', 'action'=>'add')); ?> </li>
	</ul>
</div>
