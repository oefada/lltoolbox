<?php $this->set('hideSidebar', true); ?>
<div style="width: 600px; margin-left: auto; margin-right: auto; padding-bottom: 50px;">
	<h2>Import eStara Information</h2>
	<p><strong>Please only import data for a whole month.</strong></p>

	<form enctype="multipart/form-data" method="POST">
		eStara CSV File: <input name="data[estara_csv_data]" type="file" /> <input type="submit" value="Upload File" />
	</form>
	<br/><br/>
	<a href="<?php echo $this->webroot ?>clients">Return to Client List</a>
</div>
