<?php $this->pageTitle = "Accounting - Invoice Report" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'invoice'))?>
<fieldset>
<h3 class='title'>SEARCH INVOICE BY:</h3>

<div style="float: left; ">

<div class="fieldRow">
<label>Search By</label>
<?echo $form->select('searchBy', array(0 => 'Submission Date', 1 => 'Check-in Date'))?>
<div class="range">
	<?echo $datePicker->picker('condition1.value.between.0', array('label' => 'From'))?>
	<?echo $datePicker->picker('condition1.value.between.1', array('label' => 'To'))?>
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d')?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d', strtotime('+1 day'))?>"'>Today</a> | 
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 day'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>Yesterday</a> |
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 week'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>This Week</a>
</div>
</div>

<div class="controlset fieldRow" style="border: 0">
<?php 		echo $form->checkbox('paging.disablePagination');
			echo $form->label('paging.disablePagination');
			
			echo $form->checkbox('download.csv');
			echo $form->label('download.csv', 'Download as CSV');
			?>
</div>


</div>
</fieldset>
<?php echo $form->submit('Search') ?>
</div>

<div class='index'>
<?php
//TODO: put this in a helper
function sortLink($field, $title, $currentPage, $serializedFormInput, $view, $html) {
	$url = "/reports/invoice/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:$field";
        
	if (isset($view->params['named']['sortBy']) && $view->params['named']['sortBy'] == $field) {
		$dir = ($view->params['named']['sortDirection'] == 'ASC') ? 'DESC' : 'ASC';
	} else if(isset($view->params['named']['sortBy'])  && $view->params['named']['sortBy'] == $field) {
		$dir = 'DESC';
	} else {
		$dir = 'ASC';
	}
	
	$url .= "/sortDirection:$dir";
       
	return $html->link($title, $url);
}

if (!empty($results)): ?>
	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/invoice/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		
            <tr>
                <th><?=sortLink('Invoice.accountingInvoiceId', 'ID', $currentPage, $serializedFormInput, $this, $html)?></th>
    			<th><?=sortLink('Invoice.hotelName', 'Client Name', $currentPage, $serializedFormInput, $this, $html)?></th>
                <th><?=sortLink('Invoice.ticketId', 'Ticket Id', $currentPage, $serializedFormInput, $this, $html)?></th>
                <th><?=sortLink('Invoice.confirmationNumber', 'Hotel Conf. #', $currentPage, $serializedFormInput, $this, $html)?></th>
                <th><?=sortLink('Invoice.guestName', 'Guest Name', $currentPage, $serializedFormInput, $this, $html)?></th>
    	        <th><?=sortLink('Invoice.checkinDate', 'Check-in Date', $currentPage, $serializedFormInput, $this, $html)?></th>
                <th><?=sortLink('Invoice.amount', 'Amount', $currentPage, $serializedFormInput, $this, $html)?></th>
                <th><?=sortLink('Invoice.clientComments', 'Comments', $currentPage, $serializedFormInput, $this, $html)?></th>
                <th><?=sortLink('Invoice.submittedByName', 'Submitted By - Name', $currentPage, $serializedFormInput, $this, $html)?></th>
                <th><?=sortLink('Invoice.submittedByEmail', 'Submitted By - Email', $currentPage, $serializedFormInput, $this, $html)?></th>
                <th><?=sortLink('Invoice.submittedByDate', 'Submission Date', $currentPage, $serializedFormInput, $this, $html)?></th>            
            </tr> 
        
		</thead>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$r['Invoice']['accountingInvoiceId']?></td>
		<td><?=$r['Invoice']['hotelName']?></td>
        <td><?=$r['Invoice']['ticketId']?></td>
        <td><?=$r['Invoice']['confirmationNumber']?></td>
        <td><?=$r['Invoice']['guestName']?></td>
        <td><?=$r['Invoice']['checkinDate']?></td>
        <td><?=$r['Invoice']['amount']?></td>
        <td><?=$r['Invoice']['clientComments']?></td>
        <td><?=$r['Invoice']['submittedByName']?></td>
        <td><?=$r['Invoice']['submittedByEmail']?></td>
        <td><?=$r['Invoice']['submittedByDate']?></td>         		
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/invoice/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
<?php elseif (!empty($data)): ?>
<p>No results were found for the entered filters.</p>
<p><strong>Tips:</strong> If searching by client or package name, enter four or more characters.
	<br />For client and package name you can make a search term required by adding a "+" before it, exclude it by adding a "-",
	or search a complete phrase by adding quotes "" around it. By default, offers that contain any of the search terms are returned.
</p>
<?php else: ?>
	<div class='blankExample'>
		<h1>Enter some search criteria above to search offers</h1>
		<p>This offer search report will search through all current and past offers using the search criteria entered above.</p>
		<p><strong>Tips:</strong> If searching by client or package name, enter four or more characters.
			<br />For client and package name you can make a search term required by adding a "+" before it, exclude it by adding a "-",
			or search a complete phrase by adding quotes "" around it. By default, offers that contain any of the search terms in client name or package name are returned.
			<a href="#" target="_blank">Learn more</a>
		</p>
		<?=$html->image('blank_slate_examples/reports_bids.gif')?>
	</div>
<?php endif; ?>
</div>
