		<div class="input text refundRequestDiv">
			<label>Site</label>
			<?= ($refundInfo['ticket']['Ticket']['siteId'] == 2) ? 'Family Getaway'  : 'Luxury Link'; ?>
		</div>	

		<div class="input text refundRequestDiv">
			<label>Ticket Id</label>
			<?= $refundInfo['ticket']['Ticket']['ticketId']; ?>
		</div>	

		<div class="input text refundRequestDiv">
			<label>Customer</label>
			<?= $refundInfo['ticket']['Ticket']['userFirstName']; ?> <?= $refundInfo['ticket']['Ticket']['userLastName']; ?>
		</div>

		<div class="input text refundRequestDiv">
			<label>Property</label>
			<? foreach ($refundInfo['client'] as $client) { ?>
				<?= $client['Client']['name']; ?>&nbsp;
			<? } ;?>
		</div>

		<!--
		<div class="input text refundRequestDiv">
			<label>Date Purchased</label>
			<?= $refundInfo['ticket']['Ticket']['created']; ?>
		</div>
		-->

		
		<? if ($pageVersion != 'A') { ?>

			<div class="input text refundRequestDiv">
				<label>Request Created</label>
				<?= $this->data['RefundRequest']['dateCreated']; ?> by <?= $this->data['RefundRequest']['createdBy']; ?>
			</div>

			<? if ($this->data['RefundRequest']['refundRequestStatusId'] > 1) { ?>
				<div class="input text refundRequestDiv">
					<label>Request Approved</label>
					<?= $this->data['RefundRequest']['dateApproved']; ?> by <?= $this->data['RefundRequest']['approvedBy']; ?>
				</div>
			<? } ?>

			<? if ($this->data['RefundRequest']['refundRequestStatusId'] > 2) { ?>
				<div class="input text refundRequestDiv">
					<label>Request Completed</label>
					<?= $this->data['RefundRequest']['dateCompleted']; ?> by <?= $this->data['RefundRequest']['completedBy']; ?>
				</div>
			<? } ?>
		<? } ?>