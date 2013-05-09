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

<?php echo $form->create('Call', array('action' => 'popup')); ?>

<div class="interaction">
	<?php
	echo $form->hidden('timestamp', array('value' => time()));
	echo '<table id="interactionTable"><tr><td>';
	echo $form->input('interactionType', array(
		'empty' => false,
		'multiple' => 'false',
		'size' => count(Call::$interactionTypes),
		'options' => Call::$interactionTypes,
		'default' => 1,
	));
	echo '</td><td>';
	echo $form->input('contactType', array(
		'empty' => false,
		'multiple' => 'false',
		'size' => count(Call::$contactTypes),
		'options' => Call::$contactTypes,
		'default' => 1,
	));
	echo '</td></tr></table>';
    $topics = Call::$contactTopics;
    asort($topics);
	echo $form->input('contactTopic', array(
		'empty' => false,
		'multiple' => 'false',
		'size' => count($topics),
		'options' => $topics,
	));
	echo $form->input('ticketId', array('label' => 'Ticket'));
	echo $form->input('userId', array('label' => 'User'));
	echo $form->input('clientId', array('label' => 'Client'));
	echo $form->input('notes', array('type' => 'textarea'));
	echo $form->label('&nbsp;');
	echo $form->input('callId');
	echo $form->submit(isset($callIdLabel) ? 'Save Call #' . $callIdLabel : 'Save New Call',array('class'=>(isset($callIdLabel)?'editCall':'newCall')));
	?>
</div>


<div id="lastTenCalls">
	<h3>Recent Calls</h3>
	<ul>
		<?php echo !count($lastTenCalls) ? '<li>None yet.</li>' : ''; ?>
		<?php foreach ($lastTenCalls as $ltc): ?>
			<li class="blurb"><?php echo $form->submit($ltc['Call']['callId'], array('name' => 'loadTicket')); ?> &nbsp; <span><b><?php echo preg_replace('/^[^ ]* /', '', $ltc['Call']['created']); ?></b> - <?php echo $ltc['Call']['notes']; ?></span></li>
		<?php endforeach; ?>
	</ul>
</div>

<?php echo $form->end(); ?>
