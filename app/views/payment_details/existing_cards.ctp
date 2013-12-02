<legend class="handle" style="font-size:12px;">Existing Cards (<?php echo count($userPaymentSetting);?>)</legend>
<div class="collapsibleContent">
	<table style="background:whitesmoke;margin:0px;padding:0px;" cellspacing="0" cellpadding="0" border="1">
	<tr>
		<td>&nbsp;</td>
		<td><i>Card Id</i></td>
		<td><i>Name on Card</i></td>
		<td><i>Address</i></td>
		<td><i>CC Type</i></td>
		<td><i>Card Number</i></td>
		<td><i>Exp</i></td>
		<td><i>Primary</i></td>
		<td><i>Inactive</i></td>
		<td><i>Card Added</i></td>
	</tr>
	<?php
	if (count($userPaymentSetting)) {
		$counter = 0;
		foreach ($userPaymentSetting as $upsId => $upsValue) {
			if ($upsValue['inactive']) {
				//continue;
			}
			$selectPrimaryCC = $upsValue['primaryCC'] && !$upsValue['inactive'] ? 'checked' : '';
			$cardInactiveColor = $upsValue['inactive'] ? '#CC0000;' : '#009900;';
		?>
		<tr style="color: <?php echo $cardInactiveColor;?>">
			<td>
				<input
					<?php echo $selectPrimaryCC;?>
						type="radio"
						name="data[PaymentDetail][userPaymentSettingId]"
						id="PaymentDetailUserPaymentSettingId"
						value="<?php echo $upsValue['userPaymentSettingId'];?>" />
			</td>
			<td><?php echo $upsValue['userPaymentSettingId'];?></td>
			<td><?php echo $upsValue['nameOnCard'];?></td>
			<td>
				<?php echo $upsValue['address1'];?><br />
				<?php if ($upsValue['address2']) { echo $upsValue['address2'] . '<br />'; } ?>
				<?php echo $upsValue['city'] . ', ' . $upsValue['state'] . ' ' . $upsValue['postalCode'];?>
			</td>
			<td><?php echo $upsValue['ccType'];?></td>
			<td>
				<span class="<?php echo $upsValue['userPaymentSettingId'];?>">
					<?php echo $upsValue['ccToken'];?>
					<?php if ($allowViewCCFull === true): ?>
					<br/>
					<a
							href="#"
							onclick="detokenize('<?php echo $upsValue['userPaymentSettingId'];?>'); return false;"
							>View full card number</a>
					<?php endif; ?>
				</span>
			</td>
			<td><?php echo $upsValue['expMonth'] . '/' . $upsValue['expYear'];?></td>
			<td><?php echo ($upsValue['primaryCC']) ? 'Yes' : 'No';?></td>
			<td><?php echo ($upsValue['inactive']) ? '<strong>Yes</strong>' : 'No';?></td>
			<td><?php echo $upsValue['created'];?></td>
		</tr>
	<?php } } ?>
	</table>
</div>


<script type="text/javascript">
	function detokenize(userPaymentSettingId) {
		ccContainer = jQuery('span.' + userPaymentSettingId);
		jQuery.ajax({
			url: "<?php echo $this->webroot; ?>payment_details/detokenize/" + userPaymentSettingId,
			type: "GET",
			beforeSend: function() {
				ccContainer.html('Detokenizing, please wait');
			},
			success: function(response, textStatus, jqXHR){
				ccContainer.html(response);
			},
			error: function(jqXHR, textStatus, errorThrown){
				ccContainer.html('Detokenizing failed. Please notify tech.');
			}
		});
	}
</script>