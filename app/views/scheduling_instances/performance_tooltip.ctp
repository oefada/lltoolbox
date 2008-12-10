<table>
	<tr>
		<td style='font-weight: bold'>
			# Bids:
		</td>
		<td style='text-align: right'>
			<?=@$metrics[0]['numBids']?>
		</td>
	</tr>
	<tr>
		<td style='font-weight: bold'>
			# Bidders: 
		</td>
		<td style='text-align: right'>
			<?=@$metrics[0]['numUniqueBidders']?>
		</td>
	</tr>
	<tr>
		<td style='font-weight: bold'>
			Current Bid:
		</td>
		<td style='text-align: right'>
			<?=@$number->currency($metrics[0]['maxBidAmount'])?>
		</td>
	</tr>
	<tr>
		<td style='font-weight: bold'>
			Ticket Status:
		</td>
		<td style='text-align: right'>
			<?=@$metrics['TicketStatus']['ticketStatusName']?>
		</td>
	</tr>
</table>