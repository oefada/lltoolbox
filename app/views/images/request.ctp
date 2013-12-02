<?php $this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']); ?>
<div class="images view">
<h2>Image Request</h2>

<form method="POST" action="/clients/<?= $clientId; ?>/images/request">
	To:&nbsp;&nbsp;<?= $toAddress; ?>
	<br /><br />
	<input style="width: 500px;" type="text" name="msgSubject" maxlength="200" value="<?= $msgSubject; ?>">
	<br /><br />
	<textarea style="width: 600px; height: 300px;" name="msgContent"><?= $msgContent; ?></textarea>
	<br /><br />
	<input type="submit" value="Send Email">
</form>

</div>

