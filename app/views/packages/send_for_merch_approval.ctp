<? $this->pageTitle = 'Send to Merchandising for Approval'; ?>
<?php echo $ajax->form('send_for_merch_approval', 'post', array('url' => "/clients/$clientId/packages/send_for_merch_approval/$packageId", 'update' => 'MB_content', 'complete' => 'closeModalbox()'));?>
	<fieldset>
		<p>Enter any additional notes to include in your message to merchandising</p>
		<textarea name="data[additionalMessage]"></textarea><br />
		<input type="submit" value="Send to Merchandising" />
	</fieldset>
</form>
<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>