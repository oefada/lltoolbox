<?php
$this->pageTitle = $this->data['EventRegistry']['eventTitle'].$html2->c($this->data['EventRegistry']['eventRegistryId'], 'Event Registry Id:').'<br />';
?>



<div class="eventRegsitry form">
	
	<?php echo $form->create('eventRegistry', array('action'=>'/edit/' . $this->data['EventRegistry']['eventRegistryId']));?>
	
	<fieldset>
	<h2 class="title">Event Registry Owner Information</h2>
	<?php echo $form->input('EventRegistry.eventRegistryId', array('value'=>$this->data['EventRegistry']['eventRegistryId'], 'readonly' => 'readonly', 'type'=>'hidden'));?>
	<?php echo $form->input('User.userId', array('value'=>$this->data['User']['userId'], 'readonly' => 'readonly'));?>
	<?php echo $form->input('User.firstName', array('value'=>$this->data['User']['firstName'], 'readonly' => 'readonly'));?>
	<?php echo $form->input('User.lastName', array('value'=>$this->data['User']['lastName'], 'readonly' => 'readonly'));?>
	<?php echo $form->input('EventRegistry.eventTitle', array('value'=>$this->data['EventRegistry']['eventTitle'],'required'=>'required'));?>
	<?php echo $form->input('EventRegistry.dateCreated', array('value'=>$this->data['EventRegistry']['dateCreated'], 'readonly' => 'readonly'));?>
	</fieldset>
	
	<fieldset>
	<h2 class="title">Event Registry Details</h2>
	<?php echo $form->input('EventRegistry.registryUrl', array('value'=>$this->data['EventRegistry']['registryUrl'], 'readonly' => 'readonly'));?>
	<?php echo $form->input('EventRegistry.eventDate', array('value'=>$this->data['EventRegistry']['eventDate']));?>
	<?php echo $form->input('EventRegistry.registrant1_firstName', array('value'=>$this->data['EventRegistry']['registrant1_firstName']));?>
	<?php echo $form->input('EventRegistry.registrant1_lastName', array('value'=>$this->data['EventRegistry']['registrant1_lastName']));?>
	<?php echo $form->input('EventRegistry.registrant2_firstName', array('value'=>$this->data['EventRegistry']['registrant2_firstName']));?>
	<?php echo $form->input('EventRegistry.registrant2_lastName', array('value'=>$this->data['EventRegistry']['registrant2_lastName']));?>
	<?php echo $form->input('EventRegistry.registrantAddress1', array('value'=>$this->data['EventRegistry']['registrantAddress1']));?>
	<?php echo $form->input('EventRegistry.registrantAddress2', array('value'=>$this->data['EventRegistry']['registrantAddress2']));?>
	<?php echo $form->input('EventRegistry.registrantAddress3', array('value'=>$this->data['EventRegistry']['registrantAddress3']));?>
	<?php echo $form->input('EventRegistry.registrantCity', array('value'=>$this->data['EventRegistry']['registrantCity']));?>
	<?php echo $form->input('EventRegistry.registrantStateName', array('value'=>$this->data['EventRegistry']['registrantStateName']));?>
	<?php echo $form->input('EventRegistry.registrantPostalCode', array('value'=>$this->data['EventRegistry']['registrantPostalCode']));?>
	<?php echo $form->input('EventRegistry.registrantCountryCode', array('type'=>'select', 'options'=>$this->data['countries'], 'value'=>$this->data['EventRegistry']['registrantCountryCode']));?>
	<?php echo $form->input('EventRegistry.isActive', array('type'=>'select', 'options'=>array(0=>'Inactive', 1=>'Active'), 'value'=>$this->data['EventRegistry']['isActive']));?>
	</fieldset>
	 
	<?php echo $form->end('Submit');?>
</div><!-- close eventRegsitry form -->



<div class="clients index">


<h2 class="title">Donors</h2>
	
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Donation ID</th>
	<th>User ID</th>
	<th>First Name</th>
	<th>Last Name</th>
	<th>Amount Donated</th>
	<th>Date</th>
	<th>transactionId</th>
</tr>

<?php
$i = 0;
foreach ($this->data['donors'] as $donor):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td><?php echo $donor['EventRegistryDonor']['eventRegistryDonorId']; ?></td>
		<td><?php echo $donor['EventRegistryDonor']['userId']; ?></td>
		<td><?php echo $donor['User']['firstName']; ?></td>
		<td><?php echo $donor['User']['lastName']; ?></td>
		<td>$<?php echo $donor['EventRegistryDonor']['amount']; ?></td>
		<td><?php echo date('Y-M-d h:i a', strtotime($donor['EventRegistryDonor']['dateCreated'])); ?></td>
		<td><?php echo $donor['EventRegistryDonor']['transactionId']; ?></td>
	</tr>
	
<? endforeach; ?>

</table>
</div>
