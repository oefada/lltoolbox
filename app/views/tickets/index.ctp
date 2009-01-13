<?php
$this->pageTitle = 'Tickets';
if (isset($query)) {
	$html->addCrumb('Tickets', '/tickets');
	$html->addCrumb('search for '.$query);
} else {
	$html->addCrumb('Tickets');
}
$this->set('hideSidebar', true);
?>

<div id="ticket-index">
	
	<div id="ticket-search-box">
		<form action="/tickets" method="post" id="ticket-search-form" name="ticket-search-form">
		<table cellpadding="0" cellspacing="0"> 
			<tr>
				<th colspan="2">&raquo;&nbsp;Search Criteria</th>
			</tr>
			<tr>
				<td width="150">
					Start Date
				</td>
				<td>
					<select name="s_start_y">
						<?php 
						for ($i = date('Y'); $i > 2005; $i--) { 
							$selected = ($k == $s_start_y) ? 'selected="selected"' : ''; 
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
							$selected = ($k == $s_end_y) ? 'selected="selected"' : ''; 
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
					Offer Type
				</td>
				<td>
					<select name="s_offer_type_id"> 
						<option value="0">All</option>
						<?php 
						foreach ($offerType as $k=>$v) {  
							$selected = ($k == $s_offer_type_id) ? 'selected="selected"' : ''; 
							echo "<option value=\"$k\" $selected>$v</option>\n"; 
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="150">
					Ticket Status
				</td>
				<td>
					<select name="s_ticket_status_id"> 
						<option value="0">All</option>
						<?php 
						foreach ($ticketStatus as $k=>$v) {  
							$selected = ($k == $s_ticket_status_id) ? 'selected="selected"' : ''; 
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
					User Id
				</td>
				<td>
					<input type="text" id="s_user_id" name="s_user_id" value="<?php echo $s_user_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_user_id').value = '';">Clear</a>
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
		
<table cellpadding="0" cellspacing="0" class="tickets-view-td">
<tr>
	<th width="10"><?php echo $paginator->sort('Ticket Id', 'Ticket.ticketId');?></th>
	<th width="10"><?php echo $paginator->sort('Ticket Created', 'Ticket.created');?></th>
	<th width="10"><?php echo $paginator->sort('Status', 'Ticket.ticketStatusId');?></th>
	<th width="10"><?php echo $paginator->sort('Offer Type', 'Ticket.offerTypeId');?></th>
	<th width="10"><?php echo $paginator->sort('Offer Id', 'Ticket.offerId');?></th>
	<th width="10"><?php echo $paginator->sort('Client Id', 'Client.clientId');?></th>
	<th><?php echo $paginator->sort('Client Name', 'Client.name');?></th>
	<th width="10"><?php echo $paginator->sort('User Id', 'Ticket.userId');?></th>
	<th><?php echo $paginator->sort('User First Name', 'Ticket.userFirstName');?></th>
	<th><?php echo $paginator->sort('User Last Name', 'Ticket.userLastName');?></th>
	<th class="actions"><?php __('Actions');?></th>
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
			<?php echo $ticket['TicketStatus']['ticketStatusName']; ?>
		</td>
		<td>
			<?php echo $offerType[$ticket['Ticket']['offerTypeId']]; ?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['offerId'];?>
		</td>
		<td>
			<?php echo $ticket['Client']['clientId'];?>
		</td>
		<td>
			<?php echo $ticket['Client']['name'];?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['userId'];?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['userFirstName']; ?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['userLastName']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('controller' => 'tickets', 'action'=>'view', $ticket['Ticket']['ticketId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'tickets-index'))?>

</div>