<div class="index">
	<?php if (isset($experiments)): ?>
	<h1>Site Experiments</h1>
	<form id="site_chooser" action="<?php echo $this->webroot ?>reports/experiments" method="POST">
		<select id="sites" name="site_id" onchange="getElementById('site_chooser').submit()">
			<option value="0">Select a Site</option>
			<option value="1" <?php if (isset($_POST['site_id']) AND $_POST['site_id'] == 1): ?>selected<?php endif ?>>Luxury Link</option>
			<option value="2" <?php if (isset($_POST['site_id']) AND $_POST['site_id'] == 2): ?>selected<?php endif ?>>Family Getaway</option>
		</select>
	</form>
	<table style="width: 500px; margin: 20px 0 20px 0;">
		<tr>
			<th>Experiment Name</th>
			<th>Site</th>
		</tr>
		<?php foreach($experiments as $key => $experiment): ?>
		<tr>
			<td><a href="experiments/<?php echo $experiment['Experiment']['id'] ?>"><?php echo $experiment['Experiment']['name']?></a></td>
			<td><?php echo $experiment['Site']['siteName']?></td>
		</tr>
		<?php endforeach; ?>
	</table>
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
			<th>Completed</th>
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
		
	</table>
	
	<a href="<?php echo $this->webroot ?>reports/experiments">Return to experiments</a>
	<?php endif; ?>
</div>