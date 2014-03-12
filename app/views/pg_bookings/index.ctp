<?php
$this->pageTitle = 'Pegasus Bookings';
if (isset($query)) {
	$html->addCrumb('Bookings', '/pg_bookings');
	$html->addCrumb('search for '.$query);
} else {
    $html->addCrumb('Bookings');
}
$this->set('hideSidebar', true);
?>

<div id="ticket-index">
	
	<div id="ticket-search-box">
		<form action="/pg_bookings" method="post" id="ticket-search-form" name="ticket-search-form">
		
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
					Booking Status
				</td>
				<td>
					<select name="s_booking_status_id"> 
						<option value="0">All</option>
					</select>
				</td>
			</tr>
			</table>
		</td>
		<td style="width:370px; border-left:1px solid silver; padding-left: 15px;">

			<table cellspacing="0" cellpadding="0" style="width:355px;">
			<tr>
				<td width="120">
					Booking Id
				</td>
				<td>
                    <input type="text" id="s_booking_id" name="s_booking_id" value="<?php echo $s_booking_id;?>" />
                    &nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_booking_id').value = '';">Clear</a>
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
					Package Id
				</td>
				<td>
					<input type="text" id="s_package_id" name="s_package_id" value="<?php echo $s_package_id;?>" />
					&nbsp;<a href="javascript:void(0);" onclick="document.getElementById('s_package_id').value = '';">Clear</a>
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

	<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'booking-index', 'showCount' => true)); ?>

<div class="tickets index">
	<?php if (isset($query) && !empty($query)): ?>
		<div style="clear: both">
		<strong>Search Criteria:</strong> <?php echo $query; ?>
		</div>
	<?php endif ?>
		
<table cellpadding="0" cellspacing="0" class="tickets-view-td" style="font-size:11px;">
<tr>
	<th width="10"><?php echo $paginator->sort('Booking Id', 'PgBooking.pgBookingId');?></th>
	<th width="10" style="color:#FFF;">TLD</th>	
    <th width="10"><?php echo $paginator->sort('Created', 'PgBooking.dateCreated');?></th>
	<th width="220" style="color:#FFF;">Client</th>
	<th width="220" style="color:#FFF;">User</th>
    <th width="220"  style="color:#FFF;">Traveler</th>
    <th width="220"  style="color:#FFF;">Status</th>
	<th width="220" style="color:#FFF;">Notes</th>
	<th class="actions" style="color:#FFF;"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;

foreach ($bookings as $booking):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
        <td><?php echo $booking['PgBooking']['pgBookingId']; ?></td>
        <td><?php echo ($booking['PgBooking']['tldId'] == 2) ? '.CO.UK' : '.COM'; ?></td>
        <td><?php echo $booking['PgBooking']['dateCreated'];?></td>
        <td><?php echo $booking['Client']['name'];?></td>
        <td><?php echo $booking['User']['firstName'];?> <?php echo $booking['User']['lastName'];?></td>
        <td><?php echo $booking['PgBooking']['travelerFirstName'];?> <?php echo $booking['PgBooking']['travelerLastName'];?></td>
        <td><?php echo $bookingStatusDisplay[$booking['PgBooking']['pgBookingStatusId']];?></td>
        <td></td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('controller' => 'pg_bookings', 'action'=>'view', $booking['PgBooking']['pgBookingId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'booking-index', 'showCount' => true))?>
</div>
