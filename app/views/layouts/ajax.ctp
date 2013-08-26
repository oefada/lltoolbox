<?php $session->flash();
$session->flash('error');
$session->flash('success');
$session->flash('auth');
?>
<?php print $content_for_layout; ?>