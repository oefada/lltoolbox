<?php
$appendClass = "input-with-livesearch";
$randClass = "ls-".rand(100,1000);

if (!isset($name) || !isset($controller)) {
	echo "Element Input_Search is missing requirements!";
	exit;
}

$ar['autocomplete'] = "off";

if (isset($label)) {
	$ar['label'] = $label;
}
if (isset($style)) {
	$ar['style'] = $style;
}

$ar['after'] = '<div id="'.$randClass.'" class="auto_complete_input auto_complete"></div>'; 
?>

<div class="<?= $appendClass." ".$randClass ?>">
	<?= $form->input($name,$ar); ?>
</div>
<?= $javascript->link('livesearch'); ?>
<script type="text/javascript">
	jQuery('.<?= $randClass ?> input[type="text"]').liveSearch({placeInput: true, id: "<?= $randClass ?>", url: "/ajax_search?searchtype=<?= $controller ?>"});
</script>