<fieldset class="collapsible">
	<h3 class="handle">Step 6 - Approvals</h3>
	<div class="collapsibleContent controlset2">
		<fieldset style="border: none">
		<label>Approved By Client</label>
		<div class="controlset" style="float: left; clear: none; width: 100px;">
			<?php echo $form->radio('approvedByClient', array('value' => 'Y')); ?><br />
			<?php echo $form->radio('approvedByClient', array('value' => 'N')); ?>
		</div>
		<div style="float: left; clear: none; margin: 0; padding:0">
			<?php echo $form->input('approvedByClientNotes', array('label' => false))?>
		</div>
		</fieldset>
		<fieldset style="border: none">
		<label>Internal Approval</label>
		<div class="controlset" style="float: left; width: 100px; clear: none">
			<?php echo $form->radio('internalApproval', array('value' => 'Y')); ?><br />
			<?php echo $form->radio('internalApproval', array('value' => 'N')); ?>
		</div>
		
		<div style="float: left; clear: none; margin: 0; padding: 0">
			<?php echo $form->input('internalApprovalNotes', array('label' => false))?>
		</div>
		</fieldset>
	</div>
</fieldset>