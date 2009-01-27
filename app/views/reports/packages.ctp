<?php $this->pageTitle = "Package Search" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'packages'))?>
<fieldset>
<h3 class='title'>SEARCH PACKAGES BY:</h3>

<div style="float: left; border-right: 1px solid #000; padding-right: 25px">
	<div class="fieldRow">
<?echo $form->select('condition1.field', $condition1Options)?>
<?echo $form->text('condition1.value', array('style' => 'width: 250px'))?>
	</div>
</div>

<div style="float: left; clear: none; padding-left: 25px">
	<div class="fieldRow">
	<label>Package Status</label>
	<?php echo $form->text('condition4.field', array('value' => 'Package.packageStatusId', 'type' => 'hidden'))?>
	<?php echo $form->select('condition4.value', $packageStatusIds)?>
	</div>
	
	<div class="fieldRow">
	<label>LOA Track</label>
	<?php echo $form->text('condition5.field', array('value' => 'Track.revenueModelId', 'type' => 'hidden'))?>
	<?php echo $form->select('condition5.value', $revenueModelIds, null, array('multiple' => true))?>
	</div>	
</div>

<div class="controlset fieldRow" style="border: 0">
<?php 		echo $form->checkbox('paging.disablePagination');
			echo $form->label('paging.disablePagination');?>
</div>


</fieldset>
<?php echo $form->submit('Search') ?>
</div>

<div class='index'>
<?php
//TODO: put this in a helper
function sortLink($field, $title, $currentPage, $serializedFormInput, $view, $html) {
	$url = "/reports/packages/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:$field";

	if (isset($view->params['named']['sortBy']) && $view->params['named']['sortBy'] == $field) {
		$dir = ($view->params['named']['sortDirection'] == 'ASC') ? 'DESC' : 'ASC';
	} elseif(isset($view->params['named']['sortBy'])  && $view->params['named']['sortBy'] == $field) {
		$dir = 'DESC';
	} else {
		$dir = 'ASC';
	}
	
	$url .= "/sortDirection:$dir";
	
	return $html->link($title, $url);
}

if (!empty($results)): ?>
	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/packages/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<tr>
			<th><?=sortLink('Client.name', 'Client Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.packageName', 'Package Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('PackageStatus.packageStatusName', 'Package Status', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('RevenueModel.revenueModelName', 'Track', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Client.managerUsername', 'Manager Username', $currentPage, $serializedFormInput, $this, $html)?></th>
		</tr>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$html->link($r['Client']['name'], array('controller' => 'clients', 'action' => 'edit', $r['Client']['clientId']))?></td>
		<td><?=$html->link($r['Package']['packageName'], array('controller' => 'packages', 'action' => 'edit', $r['Package']['packageId']))?></td>
		<td><?=$r['PackageStatus']['packageStatusName']?></td>
		<td><?=$r['RevenueModel']['revenueModelName']?></td>
		<td><?=$r['Client']['managerUsername']?></td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/packages/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
<?php elseif (!empty($data)): ?>
<p>No results were found for the entered filters.</p>
<p><strong>Tips:</strong> If searching by client or package name, enter four or more characters.
	<br />For client and package name you can make a search term required by adding a "+" before it, exclude it by adding a "-",
	or search a complete phrase by adding quotes "" around it. By default, offers that contain any of the search terms are returned.
</p>
<?php else: ?>
	<div class='blankExample'>
		<h1>Enter some search criteria above to search packages</h1>
		<p>This package search report will search through all current and past offers using the search criteria entered above.</p>
		<p><strong>Tips:</strong> If searching by client or package name, enter four or more characters.
			<br />For client and package name you can make a search term required by adding a "+" before it, exclude it by adding a "-",
			or search a complete phrase by adding quotes "" around it. By default, offers that contain any of the search terms in client name or package name are returned.
			<a href="#" target="_blank">Learn more</a>
		</p>
		<?=$html->image('blank_slate_examples/reports_packages.gif')?>
	</div>
<?php endif; ?>
</div>