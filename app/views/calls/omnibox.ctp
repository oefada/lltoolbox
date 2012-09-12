<h2>OmniBox</h2>
<div>
	<?php echo $form->create('Call', array(
		'action' => 'omnibox',
		'target' => '_cssearch',
	));
	echo $form->input('Search');
	echo $form->end();
	?>
</div>

<div>Search term: <?php echo $search; ?></div>

<div><pre style="background:#ffeeee;">PERSON</pre></div>
<div><pre style="background:#ffeeee;">CLIENT</pre></div>
<div><pre style="background:#ffeeee;">TICKET</pre></div>
