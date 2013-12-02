<? $this->pageTitle = 'Send to Merchandising for Approval'; ?>
<form action="/clients/<?=$clientId;?>/packages/send_for_merch_approval/<?=$packageId;?>" method="post">
	<fieldset>
		<p>Enter any additional notes to include in your message to merchandising</p>
		<textarea name="data[additionalMessage]"></textarea><br />
		<input type="submit" value="Send to Merchandising" />
	</fieldset>
</form>
