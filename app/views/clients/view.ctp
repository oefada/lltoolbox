<?php
$this->pageTitle = 'Clients';
$html->addCrumb('Clients', '/clients');
$html->addCrumb($client['Client']['name']);
?>
<?=$layout->blockStart('header');?>
	<a href="/clients/edit/<?=$client['Client']['clientId']?>" title="Edit Client" class="button edit"><span><b class="icon"></b>Edit Client</span></a>
<?=$layout->blockEnd();?>
<?=$layout->blockStart('sidebar');?>
Related Client Items
<?= $html->link('<span>Accolades ('.count($client['Accolade']).')</span>','/clients/'.$client['Client']['clientId'].'/loas', array('class' => 'button'), false, false); ?>
<?= $html->link('<span>Addresses ('.count($client['Address']).')</span>','/clients/'.$client['Client']['clientId'].'/loas', array('class' => 'button'), false, false); ?>
<?= $html->link('<span>Amenities ('.count($client['Amenity']).')</span>','/clients/'.$client['Client']['clientId'].'/loas', array('class' => 'button'), false, false); ?>
<?= $html->link('<span>LOAs ('.count($client['Loa']).')</span>','/clients/'.$client['Client']['clientId'].'/loas', array('class' => 'button'), false, false); ?>
<?= $html->link('<span>Users ('.count($client['User']).')</span>','/clients/'.$client['Client']['clientId'].'/loas', array('class' => 'button'), false, false); ?>
<?=$layout->blockEnd();?>
<div class="clients view">
<h2><?php echo $client['Client']['name']; ?> <span style="font-size: 70%; color: #777;"><span style="color: #ccc">(</span>id <?php echo $client['Client']['clientId']; ?><span style="color: #ccc">)</span></span></h2>
<table>
	<tr>
		<th><?php __('Client Type'); ?></th>
		<th><?php __('Client Level'); ?></th>
		<th><?php __('Client Status'); ?></th>
	</tr>
	<tr>
		<td>
			<?php echo $client['ClientType']['clientTypeName']; ?>
		</td>
		<td>
			<?php echo$client['ClientLevel']['clientLevelName']; ?>
		</td>
		<td>
			<?php echo $client['ClientStatus']['clientStatusName']; ?>
		</td>
	</tr>
</table>
	<dl>
<fieldset>
	<legend>Contact Details</legend>
	<dt><?php __('Email'); ?></dt>
	<dd>
		<?php echo $client['Client']['email']; ?>
		&nbsp;
	</dd>
	<dt><?php __('Phone1'); ?></dt>
	<dd>
		<?php echo $client['Client']['phone1']; ?>
		&nbsp;
	</dd>
	<dt><?php __('Phone2'); ?></dt>
	<dd>
		<?php echo $client['Client']['phone2']; ?>
		&nbsp;
	</dd>
</fieldset>
<fieldset>
	<legend>Company/Hotel Details</legend>
	<dt><?php __('Region'); ?></dt>
	<dd>
		<?php echo $html->link($client['Region']['regionName'], array('controller'=> 'regions', 'action'=>'view', $client['Region']['regionId'])); ?>
		&nbsp;
	</dd>
	<dt><?php __('Acquisition Source'); ?></dt>
	<dd>
		<?php echo $html->link($client['ClientAcquisitionSource']['clientAcquisitionSourceName'], array('controller'=> 'client_acquisition_sources', 'action'=>'view', $client['ClientAcquisitionSource']['clientAcquisitionSourceId'])); ?>
		&nbsp;
	</dd>

	<dt><?php __('Company'); ?></dt>
	<dd>
		<?php echo $client['Client']['companyName']; ?>
		&nbsp;
	</dd>

	<dt><?php __('CheckRateUrl'); ?></dt>
	<dd>
		<?php echo $client['Client']['checkRateUrl']; ?>
		&nbsp;
	</dd>
	<dt><?php __('Url'); ?></dt>
	<dd>
		<?php echo $client['Client']['url']; ?>
		&nbsp;
	</dd>
	<dt><?php __('NumRooms'); ?></dt>
	<dd>
		<?php echo $client['Client']['numRooms']; ?>
		&nbsp;
	</dd>
	
</fieldset>
<fieldset>
	<legend>Geographic Details</legend>
	<dt><?php __('Country'); ?></dt>
	<dd>
		<?php echo $client['Client']['country']; ?>
		&nbsp;
	</dd>
	<dt><?php __('AirportCode'); ?></dt>
	<dd>
		<?php echo $client['Client']['airportCode']; ?>
		&nbsp;
	</dd>
	<dt><?php __('Lat'); ?></dt>
	<dd>
		<?php echo $client['Client']['customMapLat']; ?>
		&nbsp;
	</dd>
	<dt><?php __('Long'); ?></dt>
	<dd>
		<?php echo $client['Client']['customMapLong']; ?>
		&nbsp;
	</dd>
	<dt><?php __('ZoomMap'); ?></dt>
	<dd>
		<?php echo $client['Client']['customMapZoomMap']; ?>
		&nbsp;
	</dd>
	<dt><?php __('ZoomSat'); ?></dt>
	<dd>
		<?php echo $client['Client']['customMapZoomSat']; ?>
		&nbsp;
	</dd>
</fieldset>
</div>