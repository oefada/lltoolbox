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
		
		<table cellpadding="0" cellspacing="0" style="border:1px solid silver;">
		<tr>
			<th colspan="4" style="border-bottom:1px solid silver;">&raquo;&nbsp;Search Criteria</th>
		</tr>
		<tr>
		<td style="width:350px;">

		<table cellpadding="0" cellspacing="0" style="width:300px;"> 
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
					Site
				</td>
				<td>
					<select name="s_site_id"> 
						<option value="0">All</option>
						<?php 
						foreach ($siteIds as $k=>$v) {  
							$selected = ($k == $s_site_id) ? 'selected="selected"' : ''; 
							echo "<option value=\"$k\" $selected>$v</option>\n"; 
						}
						?>
					</select>
				</td>
			</tr>
            <tr>
                <td width="150">TLD</td>
                <td>
                    <select name="s_tld_id"> 
                        <option value="0">All</option>
                        <option value="1" <? if ($s_tld_id == 1) { echo 'selected="selected"'; } ?>>.COM</option>
                        <option value="2" <? if ($s_tld_id == 2) { echo 'selected="selected"'; } ?>>.CO.UK</option>
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
				<td colspan="2" style="padding-left: 0px;">
				<table>
					<tr>
						<td>Res Check-In Date (via Date Range)</td>
						<td>
							<input style="width: 30px;" type="checkbox" id="s_res_check_in_date" name="s_res_check_in_date" <?php if ($s_res_check_in_date) { echo 'checked="checked"'; }?> />
						</td>
					</tr>
					<tr>
						<td>Has Promo</td>
						<td>
							<input style="width: 30px;" type="checkbox" id="s_has_promo" name="s_has_promo" <?php if ($s_has_promo) { echo 'checked="checked"'; }?> />
						</td>
					</tr>
					<tr>
						<td>Manual Ticket</td>
						<td>
							<input style="width: 30px;" type="checkbox" id="s_manual_ticket" name="s_manual_ticket" <?php if ($s_manual_ticket) { echo 'checked="checked"'; }?> />
						</td>
					</tr>
				</table>
				</td>
			</tr>

			</table>
		
		</td>
		<td style="width:370px; border-left:1px solid silver; padding-left: 15px;">

			<table cellspacing="0" cellpadding="0" style="width:355px;">
			<tr>
				<td width="120">
					Ticket Id
				</td>
				<td>
					<input type="text" id="s_ticket_id" name="s_ticket_id" value="<?php echo $s_ticket_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_ticket_id').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="120">
					Offer Id
				</td>
				<td>
					<input type="text" id="s_offer_id" name="s_offer_id" value="<?php echo $s_offer_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_offer_id').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="120">
					Client Id
				</td>
				<td>
					<input type="text" id="s_client_id" name="s_client_id" value="<?php echo $s_client_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_client_id').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="120">
					User Id
				</td>
				<td>
					<input type="text" id="s_user_id" name="s_user_id" value="<?php echo $s_user_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_user_id').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="120">
					Bid Id
				</td>
				<td>
					<input type="text" id="s_bid_id" name="s_bid_id" value="<?php echo $s_bid_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_bid_id').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="120">
					Package Id
				</td>
				<td>
					<input type="text" id="s_package_id" name="s_package_id" value="<?php echo $s_package_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_package_id').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="120">
					Res. Confirmation #
				</td>
				<td>
					<input style="width:150px;" type="text" id="s_res_confirmation_num" name="s_res_confirmation_num" value="<?php echo $s_res_confirmation_num;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_res_confirmation_num').value = '';">Clear</a>
				</td>
			</tr>
			<tr>
				<td width="120">
					Promo Code
				</td>
				<td>
					<input style="width:150px;" type="text" id="s_promo_code" name="s_promo_code" value="<?php echo $s_promo_code;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_promo_code').value = '';">Clear</a>
				</td>
			</tr>
			</table>

		</td>
		<td style="border-left:1px solid silver;width:300px;padding-left:15px;">
			<table cellspacing="0" cellpadding="0" style="width:285px;">
			<tr><td><strong>Quick Links</strong></td></tr>
			<tr>
				<td><a href="/pg_bookings">Pegasus Tickets</a></td>
			</tr>
			<tr>
				<td><a href="/tickets/index/?s_ticket_status_id=3&s_format_id=1&s_quick_link=1">Auction Res. Requests by Most Recent Date</a></td>
			</tr>
			<tr>
				<td><a href="/tickets/add/">Create Manual Ticket</a></td>
			</tr>
			<tr>
				<td><a href="/tickets/add2012/">Create Manual Ticket *New*</a></td>
			</tr>
			<tr>
				<td><a href="<?=$csv_link_string;?>">Export <?=$this->params['paging']['Ticket']['count']>10000?'10,000 of ':'';?><?=number_format($this->params['paging']['Ticket']['count']);?> records to CSV</a><br/></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><a href="/tickets/index/?s_quick_link=2">Auctions with > 1 Res. request</a></td>
			</tr>
			<tr>
				<td><a href="/tickets/index/?s_quick_link=3">Buy Now with > 1 Res. request</a></td>
			</tr>
			<tr>
				<td><a href="/tickets/index/?s_quick_link=4">Pending Manual Tickets</a></td>
			</tr>
			</table>
		</td>
		<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4" style="border-top:1px solid silver;background-color: #F1F1F1;">
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
	<th width="10" style="color:#FFF;">TLD</th>	
	<th width="10"><?php echo $paginator->sort('Site', 'Ticket.siteId');?></th>
	<th width="10"><?php echo $paginator->sort('Ticket Created', 'Ticket.created');?></th>
	<th width="10"><?php echo $paginator->sort('Offer Type', 'Ticket.offerTypeId');?></th>
	<th width="220" style="color:#FFF;">Client</th>
	<th width="220" style="color:#FFF;">User</th>
	<th width="10"><?php echo $paginator->sort('Sale Price', 'Ticket.billingPrice');?></th>
    <th width="10"><?php echo $paginator->sort('# Nights', 'Ticket.numNights');?></th>
	<th width="10"><?php echo $paginator->sort('Status', 'Ticket.ticketStatusId');?></th>
	<th width="10"><?php echo $paginator->sort('Res. Request Date', 'PpvNotice.emailSentDatetime');?></th>
	<th width="10" style="color:#FFF;">CC</th>	
	<th width="140" style="color:#FFF;">Res. Preferred Date</th>	
	<th width="220" style="color:#FFF;">Notes</th>
	<th width="10" style="color:#FFF;">Promo</th>
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
			<?php //echo $ticket['Ticket']['ticketId']; 
            
                //hotel beds hack get parent clientid; if hotel beds, display HB 
                $clientTicket = '';
                if ($ticket['Client']) {                       
                    foreach ($ticket['Client'] as $client) {
                        $clientTicket = $client['Client']['parentClientId'];
                    }               
                }
                
                if ($clientTicket == 11080) {
                    echo $ticket['Ticket']['ticketId'].'HB'; 
                } else {
                    echo $ticket['Ticket']['ticketId'];                              
                }
            ?>
		</td>
		<td>
			<?php echo ($ticket['Ticket']['tldId'] == 2) ? '.CO.UK' : '.COM'; ?>
		</td>
		<td>
			<?php echo $siteIds[$ticket['Ticket']['siteId']]; ?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['created'];?>
		</td>
		<td>
		<?php 
		if (isset($offerType[$ticket['Ticket']['offerTypeId']])) {
			echo $offerType[$ticket['Ticket']['offerTypeId']]; 
		}
		?>
		</td>
		<td>
			<?php foreach ($ticket['Client'] as $client) : ?>
			<a href="http://www.luxurylink.com/portfolio/por_offer_redirect.php?pid=<?php echo $client['Client']['clientId'];?>" target="_BLANK"><?php echo $client['Client']['clientId'];?></a> - <?php echo $client['Client']['name'];?>
			<br /><br />
			<?php endforeach; ?>
		</td>
		<td>
			<a href="/users/view/<?php echo $ticket['Ticket']['userId'];?>" target="_BLANK"><?php echo $ticket['Ticket']['userId'];?></a> - <?php echo $ticket['Ticket']['userFirstName']; ?> <?php echo $ticket['Ticket']['userLastName']; ?>
		</td>
		<td>
			<?php
                if ($ticket['Ticket']['tldId'] == 1) {
                    echo $number->currency($ticket['Ticket']['billingPrice']);
                } else {
                    echo $number->currency($ticket['Ticket']['billingPriceTld'], 'GBP');
                }
            ?>
		</td>
        <td>
			<?php echo $ticket['Ticket']['numNights'];?>
		</td>
		<td style="font-weight:bold;">
			<?php echo $ticket['TicketStatus']['ticketStatusName']; ?>
		</td>
		<td>
			<?php echo isset($ticket[0]['emailSentDatetime']) ? $ticket[0]['emailSentDatetime'] : ''; ?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['validCard']; ?>
		</td>
		<td <?php if (!empty($ticket['ResPreferDate']) && $ticket['ResPreferDate']['flagged'] == 1) { echo "style='background-color:#990000;color:#FFFFFF;font-weight:bold;'";}?> >
			<?php if (!empty($ticket['ResPreferDate'])) : ?>
			Check-in: <?php echo $ticket['ResPreferDate']['arrival']; ?><br />
			Check-out: <?php echo $ticket['ResPreferDate']['departure']; ?>
			<?php endif; ?>
		</td>
		<td>
			<?php if (!empty($ticket['Promo'])) :?>
			<?php foreach ($ticket['Promo'] as $t_promo) : ?>

			<h3 style="margin:0px;padding:0px;padding-bottom:5px;">** Promo Code [<?=$t_promo['pc']['promoCode'];?>] **</h3>
			<h3 style="margin:0px;padding:0px;padding-bottom:5px;">
				<?php if ($t_promo['p']['amountOff']) : ?>
				Amount Off: <?php echo $number->currency($t_promo['p']['amountOff']);?>
				<?php endif; ?>
				<?php if ($t_promo['p']['percentOff']) : ?>
				Percent Off: <?php echo $number->currency($t_promo['p']['percentOff']);?>
				<?php endif; ?>
			</h3>

			<?php endforeach; ?>
			<?php endif; ?>
			<?php echo str_replace("\n",'<br/>',$ticket['Ticket']['ticketNotes']); ?>
		</td>
		<td>
		<?
		if (isset($ticket['Promo'][0]['pc']['promoCode'])) echo $ticket['Promo'][0]['pc']['promoCode'];
		?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('controller' => 'tickets', 'action'=>'view', $ticket['Ticket']['ticketId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'ticket-index', 'showCount' => true))?>

</div>
