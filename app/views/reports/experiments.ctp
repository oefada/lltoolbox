<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript">
	$.noConflict();
</script>
<div class="index">
	<?php if (isset($experiments)): ?>
	<h1>Site Experiments</h1>
	<form id="site_chooser" action="<?php echo $this->webroot ?>reports/experiments" method="POST">
		<select id="sites" name="site_id" onchange="getElementById('site_chooser').submit()">
			<option value="0">Select a Site</option>
			<option value="1" <?php if (isset($_POST['site_id']) AND $_POST['site_id'] == 1): ?>selected<?php endif ?>>Luxury Link</option>
			<option value="2" <?php if (isset($_POST['site_id']) AND $_POST['site_id'] == 2): ?>selected<?php endif ?>>Family Getaway</option>
			<option value="3" <?php if (isset($_POST['site_id']) AND $_POST['site_id'] == 3): ?>selected<?php endif ?>>Vacationist</option>
		</select>
	</form>
	<table style="width: 800px; margin: 20px 0 20px 0;">
		<tr>
			<th>Status</th>
			<th>Site</th>
			<th>Experiment Name</th>
			<th>Test %</th>
			<th>Created</th>
			<th>Last Test</th>
		</tr>
		<?php foreach($experiments as $key => $experiment): ?>
		<tr>
			<td align="center">
				<select class="experiment_status">
					<option class="experiment_status_for_id_<?php echo $experiment['Experiment']['id'] ?>" value="1" <?php if ($experiment['SitesExperiments']['status'] == 1): ?>selected<?php endif; ?>>Enabled</option>
					<option class="experiment_status_for_id_<?php echo $experiment['Experiment']['id'] ?>" value="2" <?php if ($experiment['SitesExperiments']['status'] == 2): ?>selected<?php endif; ?>>Control</option>
					<option class="experiment_status_for_id_<?php echo $experiment['Experiment']['id'] ?>" value="3" <?php if ($experiment['SitesExperiments']['status'] == 3): ?>selected<?php endif; ?>>Alternate</option>
				</select>
			</td>
			<td><?php echo $experiment['Site']['siteName']?></td>
			<td><a href="experiments/<?php echo $experiment['Experiment']['id'] ?>"><?php echo $experiment['Experiment']['name']?></a></td>
			<td style="text-align: center;"><?php echo $experiment['SitesExperiments']['test_percentage']?></td>
			<td><?php echo $experiment['SitesExperiments']['created']?></td>
			<td><?php echo $experiment['SitesExperiments']['last_test']?></td>			
		</tr>
		<?php endforeach; ?>
	</table>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery("select.experiment_status").change(function(e) {
				var selectbox = jQuery(this);
				var option = selectbox.find("option:selected");
				var experiment_id = option.attr("class").substring(25);
				var experiment_status = option.val();
				var url = "<?php echo $this->webroot ?>reports/experiment_status/" + experiment_id + "/" + experiment_status;
				selectbox.attr('disabled', 'disabled');
				jQuery.ajax({
					url: url,
					success: function() {
						selectbox.removeAttr('disabled');
					},
					error: function() {
						alert('There was an error changing the experiment status. Please reload this page and notify tech.');
					}
				});				
			});
		});		
	</script>
	<?php elseif (isset($results)): ?>
	<h1>Test Results</h1>
	<h3>Experiment "<?php echo $results['experiment_name']; ?>"</h3>
	<br/>
	<ul>
		<li><?php echo $results['total_tests']; ?> overall results</li>
		<li><?php echo $results['total_conversions']; ?> overall conversions</li>
		<li><?php echo number_format($results['conversion_rate'], 2); ?>% overall conversion rate</li>
	</ul>
	
	<table style="width: 500px; margin: 20px 0 20px 0;">
		<tr>
			<th>Treatment</th>
			<th>Tested</th>
			<th>Converted</th>
			<th>Conversion Rate</th>
			<th>Z-Score</th>
		</tr>
		<tr>
			<td>alternate</td>
			<td><?php echo $results['alt_treatments_tested']; ?></td>
			<td><?php echo $results['alt_treatments_completed']; ?></td>
			<td><?php echo number_format($results['alt_conversion_rate'], 2); ?>%</td>
			<td><?php echo number_format($results['alt_z_score'], 2); ?></td>
		</tr>
		<tr>
			<td>default</td>
			<td><?php echo $results['default_treatments_tested']; ?></td>
			<td><?php echo $results['default_treatments_completed']; ?></td>
			<td><?php echo number_format($results['default_conversion_rate'], 2); ?>%</td>
			<td><?php echo $results['default_z_score']; ?></td>
		</tr>
		<? if ($results['untested_treatments_tested']) { ?>
			<tr>
				<td>untested</td>
				<td><?php echo $results['untested_treatments_tested']; ?></td>
				<td><?php echo $results['untested_treatments_completed']; ?></td>
				<td><?php echo number_format($results['untested_conversion_rate'], 2); ?>%</td>
				<td></td>
			</tr>
		<? } ?>
		<? if ($results['bot_treatments_tested']) { ?>
			<tr>
				<td>bot</td>
				<td><?php echo $results['bot_treatments_tested']; ?></td>
				<td><?php echo $results['bot_treatments_completed']; ?></td>
				<td><?php echo number_format($results['bot_conversion_rate'], 2); ?>%</td>
				<td></td>
			</tr>
		<? } ?>
	</table>
	test % <input type="text" id="f_test_percentage" value="<?php echo $test_percentage; ?>" style="width:40px;">&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" id="btn_test_percentage">Update</a>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery("#btn_test_percentage").click(function(e) {
				var experiment_pct = jQuery("#f_test_percentage").val();
				var url = "<?php echo $this->webroot ?>reports/experiment_testpercent/<?php echo $experiment_id; ?>/" + experiment_pct;
				window.location.href = url;				
			});
		});		
	</script>
	<br /><br />
	<a href="<?php echo $this->webroot ?>reports/experiments">Return to experiments</a>
	<?php endif; ?>
</div>