<form id="submit_cr_batch" method="post" action="<?php echo $this->webroot; ?>reports/consolidated_report_batch">
<input type="hidden" name="data[ConsolidatedReportJob][report_date]" value="<?php echo $date; ?>" />
<table border="0" style="border: 0">
<tr>
	<td border="0" style="width: 25px; border: 0" align="center"><input type="checkbox" id="toggle_selections" checked /></td>
	<td border="0" style="border: 0">Check/Uncheck All</td>
</tr>
<tr><td colspan="2" style="width: 25px; border: 0"><hr size="1" /></td></tr>
<?php foreach($clients as $client): ?>
<tr>
	<td border="0" style="width: 25px; border: 0" align="center"><input type="checkbox" name="data[ConsolidatedReportJobsClients][][client_id]" value="<?php echo $client['Client']['clientId']; ?>" class="client_checkbox" checked /></td>
	<td border="0" style="border: 0"><?php echo $client['Client']['name']; ?></td>
</tr>
<?php endforeach; ?>
</table>
</form>

<script type="text/javascript">
jQuery("input#toggle_selections").click(function() {
	jQuery("input.client_checkbox").prop("checked", jQuery(this).prop("checked"));
});
</script>