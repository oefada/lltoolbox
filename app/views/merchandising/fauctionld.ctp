<style>
	textarea {
		min-width: 400px;
		min-height: 80px;
	}
</style>
<div>
	<h3 style="font-size: 16px; padding: 0;">Featured Auction</h3>
	<h2>Listing and Destination Page Featured Auction Scheduling</h2>
	<form name="schedule-date" method="POST" action="#">
		<?php
		if (isset($lastDate)) {
			echo 'Last scheduled date: ' .$html->link(  $lastDate,'#',array('onclick'=>'jQuery(\'#scheduleDate\').val(\''.$lastDate.'\');return false;'));
		}
		?>
		<input type="hidden" name="schedule-date" value="1" />
		<?php echo $datePicker->picker('scheduleDate', array(
				'label' => 'Select a date to schedule: ',
				'value' => (isset($scheduleDate) ? $scheduleDate : '')
			));
		?>
		<input type="submit" value="Go" />
	</form>
	<div>
		<?php
		if (isset($others['current']['startDate'])) {
			echo 'Currently scheduled data: ' . $others['current']['startDate'];
			if (isset($others['next']['startDate'])) {echo '<br />';
			}
		}
		if (isset($others['next']['startDate'])) {
			echo 'Next schedule date: ' . $others['next']['startDate'];
		}
		?><br />
	</div>
	<div>
		<?php if (!isset($scheduleDate)) :
		?>
		<center>
			<h3>Please select a date to schedule</h3>
		</center>
		<br />
		<br />
		<?php else :?>
		<hr/>
		<h2>Scheduling Featured Auction for <?php echo $scheduleDate;?></h2>
		<form name="fauctionld" method="POST">
			<input type="hidden" name="data[scheduleDate]" value="<?php echo $scheduleDate;?>" />
			<input type="hidden" name="fauctionld" value="1" />
			<table style="width:auto;">
				<tr>
					<th>Listing URLs</th>
					<th>Client URLs</th>
					<th>Remove</th>
				</tr>
				<?php
$i=0;
foreach ($formData as $rowObj):
$i++;
				?>
				<tr>
					<td>					<textarea name="data[fauctionLD][<?php echo $i;?>][listing]"><?php
					if (property_exists($rowObj, 'listing')) {
						echo htmlentities(implode("\n", $rowObj->listing));
					}
						?></textarea></td>
					<td>					<textarea name="data[fauctionLD][<?php echo $i;?>][client]"><?php
					if (property_exists($rowObj, 'client')) {
						echo htmlentities(implode("\n", $rowObj->client));
					}
						?></textarea></td>
					<td nowrap>
					<br/>
					<br/>
					<input type="checkbox" name="data[fauctionLD][<?php echo $i;?>][delete]" />
					<span style="color:#660000;">Delete</span></td>
				</tr>
				<?php
				endforeach;
				$i++;
				?>
				<tr>
					<td>					<textarea></textarea></td>
					<td>					<textarea></textarea></td>
					<td nowrap>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<td>
					<br />
					<input type="submit" value="Save" />
					</td>
				</tr>
			</table>
		</form>
		<?php endif;?>
	</div>
</div>
<div>
	<h3>Debug</h3>
	<div>
		Data 		<pre><?php echo htmlentities(print_r($this->data, true));?></pre>
	</div>
	<hr/>
	<div>
		Save Data 		<pre><?php echo htmlentities(json_encode($saveData));?></pre>
	</div>
	<hr/>
	<div>
		JSON Form Data 		<pre><?php echo htmlentities(json_encode($formData));?></pre>
	</div>
	<hr/>
	<div>
		Form Data 		<pre><?php echo htmlentities(print_r($formData, true));?></pre>
	</div>
</div>
