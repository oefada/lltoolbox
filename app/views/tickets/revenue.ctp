<?php
$this->pageTitle = 'Tickets';
if (isset($query)) {
	$html->addCrumb('Tickets', '/tickets/revenue');
	$html->addCrumb('search for '.$query);
} else {
	$html->addCrumb('Tickets');
}
$this->set('hideSidebar', true);
?>

<div id="ticket-index">
	
	<div id="ticket-search-box">
		<form action="/tickets/revenue" method="post" id="ticket-search-form" name="ticket-search-form">
		<table cellpadding="0" cellspacing="0"> 
			<tr>
				<th colspan="2">&raquo;&nbsp;Search Criteria for Revenue / LOA Track</th>
			</tr>
			<tr>
				<td width="150">
					Start Date
				</td>
				<td>
					<select name="s_start_y">
						<?php 
						for ($i = date('Y'); $i > 2005; $i--) { 
							$selected = ($i == $s_start_y) ? 'selected="selected"' : ''; 
							echo "<option value=\"$i\" $selected>$i</option>\n"; 
						}
						?>
					</select>
					<select name="s_start_m">
						<?php 
						for ($i = 1; $i < 13; $i++) { 
							$pad_i = str_pad($i, 2, 0, STR_PAD_LEFT);
							$selected = ($pad_i == $s_start_m) ? 'selected="selected"' : ''; 
							echo "<option value=\"$pad_i\" $selected>$pad_i</option>\n"; 
						}
						?>
					</select>
					<select name="s_start_d">
						<?php 
						for ($i = 1; $i < 32; $i++) { 
							$pad_i = str_pad($i, 2, 0, STR_PAD_LEFT);
							$selected = ($pad_i == $s_start_d) ? 'selected="selected"' : ''; 
							echo "<option value=\"$pad_i\" $selected>$pad_i</option>\n"; 
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="150">
					End Date
				</td>
				<td>
					<select name="s_end_y">
						<?php 
						for ($i = date('Y'); $i > 2005; $i--) { 
							$selected = ($i == $s_end_y) ? 'selected="selected"' : ''; 
							echo "<option value=\"$i\" $selected>$i</option>\n"; 
						}
						?>
					</select>
					<select name="s_end_m">
						<?php 
						for ($i = 1; $i < 13; $i++) { 
							$pad_i = str_pad($i, 2, 0, STR_PAD_LEFT);
							$selected = ($pad_i == $s_end_m) ? 'selected="selected"' : ''; 
							echo "<option value=\"$pad_i\" $selected>$pad_i</option>\n"; 
						}
						?>
					</select>
					<select name="s_end_d">
						<?php 
						for ($i = 1; $i < 32; $i++) { 
							$pad_i = str_pad($i, 2, 0, STR_PAD_LEFT);
							$selected = ($pad_i == $s_end_d) ? 'selected="selected"' : ''; 
							echo "<option value=\"$pad_i\" $selected>$pad_i</option>\n"; 
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="150">
					Format Type
				</td>
				<td>
					<select name="s_format_id"> 
						<option value="0">All</option>
						<?php 
						foreach ($format as $k=>$v) {  
							$selected = ($k == $s_format_id) ? 'selected="selected"' : ''; 
							echo "<option value=\"$k\" $selected>$v</option>\n"; 
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="150">
					Ticket Id
				</td>
				<td>
					<input type="text" id="s_ticket_id" name="s_ticket_id" value="<?php echo $s_ticket_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_ticket_id').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="150">
					Offer Id
				</td>
				<td>
					<input type="text" id="s_offer_id" name="s_offer_id" value="<?php echo $s_offer_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_offer_id').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="150">
					Client Id
				</td>
				<td>
					<input type="text" id="s_client_id" name="s_client_id" value="<?php echo $s_client_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_client_id').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="150">
					&nbsp;
				</td>
				<td>
					<input type="submit" name="s_submit" value="Search" />
				</td>
			</tr>
		</table>
		</form>
	</div>

	<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'ticket-index', 'showCount' => true)); ?>

<div class="tickets index">
	<?php if (isset($query) && !empty($query)): ?>
		<div style="clear: both">
		<strong>Search Criteria:</strong> <?php echo $query; ?>
		</div>
	<?php endif ?>
		
<table cellpadding="0" cellspacing="0" class="tickets-view-td" style="font-size:11px;">
<tr>
	<th width="10"><?php echo $paginator->sort('Ticket Id', 'Ticket.ticketId');?></th>
	<th width="10"><?php echo $paginator->sort('Ticket Created', 'Ticket.created');?></th>
	<th width="10"><?php echo $paginator->sort('Offer Id', 'Ticket.offerId');?></th>
	<th width="250" style="color:#FFF;">Client</th>
	<th width="10" style="text-align:center;color:#FFF;">Ticket Amount</th>
	<th width="10" style="text-align:center;color:#FFF;">Payment Collected</th>
	<th width="10" style="text-align:center;color:#FFF;">Track Id</th>
	<th width="10" style="text-align:center;color:#FFF;">Track Name</th>
	<th width="10" style="text-align:center;color:#FFF;">Allocation Amount</th>
	<th width="10" style="text-align:center;color:#FFF;">Keep Amount</th>
	<th width="10" style="text-align:center;color:#FFF;">Remit Amount</th>
	<th width="10" style="text-align:center;color:#FFF;">Status</th>
	<th class="actions" style="color:#FFF;"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($tickets as $ticket):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $ticket['Ticket']['ticketId']; ?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['created'];?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['offerId']; ?>
		</td>
		<td>
			<?php foreach ($ticket['Client'] as $client) : ?>
			<a href="/clients/edit/<?php echo $client['Client']['clientId'];?>" target="_BLANK"><?php echo $client['Client']['clientId'];?></a> - <?php echo $client['Client']['name'];?>
			<br /><br />
			<?php endforeach; ?>
		</td>
		<td style="text-align:center;">
			<?php 
			foreach ($ticket['Tracks'] as $track) {
				echo $number->currency($ticket['Ticket']['billingPrice']) . '<br /><br />';
			}
			?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['sumPayment']; ?>
		</td>
		<td style="text-align:center;">
			<?php 
			foreach ($ticket['Tracks'] as $track) {
				echo $track['trackId'] . '<br /><br />';	
			}
			?>
		</td>
		<td style="text-align:center;">
			<?php 
			foreach ($ticket['Tracks'] as $track) {
				echo $track['trackName'] . '<br /><br />';	
			}
			?>
		</td>
		<td style="text-align:center;">
			<?php 
			foreach ($ticket['Tracks'] as $track) {
				echo $track['trackDetail']['allocatedAmount'] . '<br /><br />';
			}
			?>
		</td>
		<td style="text-align:center;">
			<?php 
			foreach ($ticket['Tracks'] as $track) {
					echo $track['trackDetail']['amountKept'] . '<br /><br />';
			}
			?>
		</td>
		<td style="text-align:center;">
			<?php 
			foreach ($ticket['Tracks'] as $track) {
				echo $track['trackDetail']['amountRemitted'] . '<br /><br />';
			}
			?>
		</td>
		<td style="text-align:center;">
			<?php 
			foreach ($ticket['Tracks'] as $track) {
				echo $html->image($track['trackDetail']['status'] == 1 ? 'tick.png' : 'cross.png') . '<br /><br />';
			}
			?>
		</td>
		
		<td class="actions">
			<a href="/tickets/<?php echo $ticket['Ticket']['ticketId'];?>/trackDetails/add" target="_BLANK">View Details</a>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'tickets-index'))?>

</div>