<?php $this->pageTitle = "LOA Package In Report";


?>
<div style="float:right;">
    <?if (!empty($results)): ?>
    <?=$html->link('<span><b class="icon"></b>Export Report</span>', array(
            'controller' => 'reports',
            'action' => $this->action.'/filter:'.urlencode($serializedFormInput),
            'ext' => 'csv',
            'format'=>'csv',
        ), array(
            'escape' => false,
            'class' => 'button excel',
        ));
    ?>
    <?endif;?>
</div>

<div class='advancedSearch' style="width: 800px">
    <?php echo $form->create('', array('action' => 'booking_report')) ?>
    <fieldset>
        <h3 class='title'>SEARCH LOAs BY:</h3>

        <div class="fieldRow">
            <label>Package Live or In Date</label>
            <? echo $form->text(
                'condition1.field',
                array('value' => "DATE_FORMAT(Loa.packageLiveDate, '%Y-%m-%d') OR=DATE_FORMAT(Loa.customerApprovalDate, '%Y-%m-%d')", 'type' => 'hidden')
            ) ?>
            <div class="range">
                <? echo $datePicker->picker('condition1.value.between.0', array('label' => 'From')) ?>
                <? echo $datePicker->picker('condition1.value.between.1', array('label' => 'To')) ?>
                <a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?= date(
                    'Y-m-d'
                ) ?>"; $("condition1valueBetween1").value = ""'>Today</a> |
                <a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?= date(
                    'Y-m-d',
                    strtotime('-1 day')
                ) ?>"; $("condition1valueBetween1").value = ""'>Yesterday</a> |
                <a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?= date(
                    'Y-m-d',
                    strtotime('-1 week')
                ) ?>"; $("condition1valueBetween1").value = "<?= date('Y-m-d') ?>"'>Past 7 Days</a> |
                <a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?= date(
                    'Y-m-d'
                ) ?>"; $("condition1valueBetween1").value = "<?= date('Y-m-d', strtotime('+1 week')) ?>"'>Next 7
                    Days</a>
            </div>
        </div>
        <div style="float: left; border-right: 1px solid #000; padding-right: 25px">
            <div class="fieldRow">
                <label>LOA Level</label>
                <?php echo $form->text('condition2.field', array('value' => 'Loa.loaLevelId', 'type' => 'hidden')) ?>
                <? echo $form->select('condition2.value', $LoaLevelsList, null, array('multiple' => true)) ?>
            </div>

        </div>

        <div style="float: left; clear: none; padding-left: 25px">
            <div class="fieldRow">
                <label>Sales Person/AM</label>
                <?php echo $form->text(
                    'condition3.field',
                    array('value' => 'Loa.accountManager', 'type' => 'hidden')
                ) ?>
                <?php echo $form->select('condition3.value', $SalesPeopleList) ?>
            </div>

            <div class="fieldRow">
                <label>Client ID</label>
                <?php echo $form->text('condition4.field', array('value' => 'Client.clientId', 'type' => 'hidden')) ?>
                <?php echo $form->text('condition4.value') ?>



                <!--	--><?php //echo $form->text('condition4.field', array('value' => 'Client.clientId', 'type' => 'hidden'))?>
                <!--	--><?php //echo $form->input('condition4.value', $revenueModelIds, null, array('multiple' => false))?>
            </div>
        </div>
        <?php
     //   echo $this->renderElement("input_search", array('name'=>'clientId', 'controller'=>'clients', 'label'=>'Client Id', 'style'=>'width:400px', 'multiSelect'=>'ClientId'));

        ?>

<div class="controlset fieldRow" style="border: 0">
<?php 		echo $form->checkbox('paging.disablePagination');
			echo $form->label('paging.disablePagination');?>
</div>


</fieldset>
<?php echo $form->submit('Search') ?>
</div>

<div class='index'>
<?php

if (!empty($results)):

	$url = "/reports/booking_report/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:";

	?>

	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/booking_report/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		<tr>
            <th><?=$utilities->sortLink('LoaLevel.loaLevelName', 'Loa Level',  $this, $html,$url)?></th>
            <th><?=$utilities->sortLink('Loa.customerApprovalDate', 'Package In Date',  $this, $html,$url)?></th>
            <th><?=$utilities->sortLink('Client.clientId', 'Client ID',  $this, $html,$url)?></th>

            <th><?=$utilities->sortLink('Client.name', 'Client Name',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.loaId', 'Loa ID',  $this, $html,$url)?></th>

            <th><?=$utilities->sortLink('AccountType.accountTypeName', 'Account Type',  $this, $html,$url)?></th>


            <th><?=$utilities->sortLink('Loa.startDate', 'Start Date',  $this, $html,$url)?></th>
            <th><?=$utilities->sortLink('Loa.endDate', 'End Date',  $this, $html,$url)?></th>

            <th><?=$utilities->sortLink('LoaMembershipType.loaMembershipTypeName', 'Membership Type',  $this, $html,$url)?></th>
            <th><?=$utilities->sortLink('LoaPaymentTerm.description', 'Payment Terms',  $this, $html,$url)?></th>
            <th><?=$utilities->sortLink('LoaInstallmentType.name', 'Installment Type',  $this, $html,$url)?></th>

            <th><?=$utilities->sortLink('Loa.membershipFee', 'Membership Fee',  $this, $html,$url)?></th>
            <th><?=$utilities->sortLink('Loa.membershipBalance', 'Membership Balance',  $this, $html,$url)?></th>


			<th><?=$utilities->sortLink('Loa.accountManager', 'AM',  $this, $html,$url)?></th>
		</tr>
		</thead>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
        <td><?=$r['LoaLevel']['loaLevelName']?></td>
        <td><?=$r['Loa']['customerApprovalDate']?date('m-d-Y',strtotime($r['Loa']['customerApprovalDate'])):'';?></td>
        <td><?=$html->link($r['Client']['clientId'], array('controller' => 'clients', 'action' => 'edit', $r['Client']['clientId']))?></td>
		<td><?=$html->link($r['Client']['name'], array('controller' => 'clients', 'action' => 'edit', $r['Client']['clientId']))?></td>
		<td><?=$html->link($r['Loa']['loaId'], array('controller' => 'loas', 'action' => 'edit', $r['Loa']['loaId']))?></td>

        <td><?=$r['AccountType']['accountTypeName']?></td>


        <td><?=$r['Loa']['startDate']?date('m-d-Y',strtotime($r['Loa']['startDate'])):'';?></td>
        <td><?=$r['Loa']['endDate']?date('m-d-Y',strtotime($r['Loa']['endDate'])):'';?></td>

        <td><?=$r['LoaMembershipType']['loaMembershipTypeName']?></td>
        <td><?=$r['LoaPaymentTerm']['description']?></td>
        <td><?=$r['LoaInstallmentType']['name']?></td>

        <td><?=$r['Loa']['membershipFee']?></td>
        <td><?=$r['Loa']['membershipBalance']?></td>

		<td><?=$r['Loa']['accountManager']?></td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/booking_report/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
<?php elseif (!empty($data)): ?>
<p>No results were found for the entered filters.</p>
<!--<p><strong>Tips:</strong> If searching by client or package name, enter four or more characters.-->
<!--	<br />For client and package name you can make a search term required by adding a "+" before it, exclude it by adding a "-",-->
<!--	or search a complete phrase by adding quotes "" around it. By default, offers that contain any of the search terms are returned.-->
<!--</p>-->
<?php else: ?>
	<div class='blankExample'>
		<h1>Enter some search criteria above to search Loas</h1>

		<?=$html->image('blank_slate_examples/reports_packages.gif')?>
	</div>
<?php endif; ?>
</div>
