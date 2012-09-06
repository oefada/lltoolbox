<div class="calls index"><h2><?php __('Calls'); ?></h2>
<ul>
<?php
foreach ($calls as $call) {
	echo '<li>' . $call['Call']['callId'] . '</li>';
}
?>
</ul>
</div>
