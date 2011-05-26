<? if(!isset($clients)): ?>
<div class="form">
<?php echo $form->create(null, array('action' => 'index'));?>
	<fieldset>
	<?php
		echo $form->input('site');
		echo $form->input('themeName');
		echo $form->input('url');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<? endif; ?>

<? if(isset($clients)): ?>
<? if(!@$emailSent):?>
<p class="notice"><strong>The following clients were found and will be emailed:</strong></p><br />
<? else: ?>
<div id="successMessage">The following clients were successfully emailed</div>
<? endif; ?>
<strong>Theme:</strong> <?=$this->data['ClientNewsletterNotifier']['themeName']?><br />
<strong>URL:</strong> <?=$this->data['ClientNewsletterNotifier']['url']?><br />
<ul>
<? foreach ($clients as $client): ?>
<li><?=$client['Client']['name']?>
	<ul>
	<? foreach ($client['ClientContact'] as $contact): ?>
		<li><?=$contact['name']?> (<?=$contact['emailAddress']?>)</li>
	<? endforeach; ?>
	</li>
	</ul>
<? endforeach;?>
</ul>

<? if(!@$emailSent):?>
<div class="form">
<?php echo $form->create(null, array('action' => 'index'));?>
	<fieldset>
	<?php
		echo $form->input('site', array('type' => 'hidden'));
		echo $form->input('themeName', array('type' => 'hidden'));
		echo $form->input('url', array('type' => 'hidden'));
		echo $form->input('approve', array('type' => 'hidden', 'value' => true));
	?>
	</fieldset>
<?php echo $form->end('Send Email');?>
</div>
<? endif;?>
<? endif;?>
