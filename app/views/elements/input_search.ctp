<?php
$appendClass = "input-with-livesearch";
$randClass = "ls-".rand(100,1000);

$liveSearchCallingId = (isset($callingId)) ? $callingId : '';

if (!isset($name) || !isset($controller)) {
	echo "Element Input_Search is missing requirements!";
	exit;
}

$ar['autocomplete'] = "off";
$ar['maxLength'] = '50';

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
<?= $javascript->link('livesearch.js?v=121121'); ?>
<script type="text/javascript">
	jQuery('.<?= $randClass ?> input[type="text"]').liveSearch({callingId: "<?= $liveSearchCallingId ?>", placeInput: true, id: "<?= $randClass ?>", url: "/ajax_search?searchtype=<?= $controller ?>"});
</script>