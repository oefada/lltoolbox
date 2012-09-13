<div>
	<div style="float: right;">
		<span class="ajaxLoadingIndicator"><?php echo $html->image('ajax-loader2.gif'); ?></span>
		<button id="newCs">
			New
		</button>
		<button id="openCsSearch">
			&gt;&gt;
		</button>
	</div>
	<div>
		<h2>CS Tool</h2>
	</div>
</div>

<div>
	<div style="float: right;"><span id="csToolClock">Clock</span></div>
	<div><span id="csUserName"><?php echo $username; ?></span></div>
</div>

<div>
	<?php echo $form->create('Call', array(
		'action' => 'omnibox',
		'target' => '_cssearch',
	));
	echo $form->input('Search', array('label' => 'Search by username, full name, email, client id, ticket id'));
	echo $form->end();
	?>
</div>

<div class="interaction">
	<?php
	echo $form->create('Call', array('action' => 'popup'));
	echo $form->hidden('timestamp', array('value' => time()));
	echo $form->hidden('username', array('value' => $username));
	echo $form->input('interaction_type', array(
		'empty' => false,
		'multiple' => 'false',
		'size' => count(Call::$interactionTypes),
		'options' => Call::$interactionTypes,
		'default' => 1,
	));
	echo $form->input('contact_type', array(
		'empty' => false,
		'multiple' => 'false',
		'size' => count(Call::$contactTypes),
		'options' => Call::$contactTypes,
		'default' => 1,
	));
	echo $form->input('contact_topic', array(
		'empty' => false,
		'multiple' => 'false',
		'size' => count(Call::$contactTopics),
		'options' => Call::$contactTopics,
	));
	echo $form->input('ticket_id');
	echo $form->input('user_id');
	echo $form->input('client_id');
	echo $form->input('notes', array('type' => 'textarea'));
	echo $form->label('&nbsp;');
	echo $form->submit('Save');
	echo $form->end();
	?>
</div>

<div id="lastTenCalls">
	<h3>Recent Calls</h3>
	<ul>
		<?php echo !count($lastTenCalls) ? '<li>None yet.</li>' : ''; ?>
		<?php foreach ($lastTenCalls as $ltc): ?>
			<li class="blurb"><a href="#"><?php echo preg_replace('/^.* /', '', $ltc['Call']['created']); ?></a> &nbsp; <span><?php echo $ltc['Call']['notes']; ?></span></li>
		<?php endforeach; ?>
	</ul>
</div>
