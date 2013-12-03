		<style>
			.refundRequestPayments {
				display: inline; 
				width: 60%; 
				border: none; 
			 }

			.refundRequestPayments td {
				border: none; 
				padding: 0 20px 10px 0;
			 }

			 .refundRequestDivider {
				width: 60%; 
				margin: 20px 0;
				border: 0;
				background-color: #ccc;
				height: 1px;
			 }

			 input.moneyWidth {
				width: 150px;
				text-align: right;
			 }
			 
		</style>

		<div class="input text refundRequestDiv">
			<label>Site</label>
			<? if ($refundInfo['ticket']['Ticket']['siteId'] == 2) {
                  echo 'Family Getaway';
               } else {
                    if ($refundInfo['ticket']['Ticket']['tldId'] == 2) {
                        echo 'Luxury Link - UK';
                    } else {
                        echo 'Luxury Link';
                    }
               }
            ?>
		</div>	

		<div class="input text refundRequestDiv">
			<label>Ticket</label>
			<?= $refundInfo['ticket']['Ticket']['ticketId']; ?> &nbsp;-&nbsp; <?= $refundInfo['ticket']['OfferType']['offerTypeName']; ?>
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
			
		<? if ($refundInfo['ticket']['Cancellation']['cancellationNotes'] != '') { ?>
			<hr class="refundRequestDivider" />
			<div class="input text refundRequestDiv">
				<label>Cancellation Notes</label>
				<?= $refundInfo['ticket']['Cancellation']['cancellationNotes']; ?>
			</div>
		<? } ?>