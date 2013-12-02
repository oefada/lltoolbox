<?php
    $this->layout = 'default_jquery';
    //debug($package);
    //die();
?>
<script type="text/javascript">
    var clientId = <?php echo $clientId; ?>;
    var packageId = <?php echo $package['Package']['packageId']; ?>;
</script>
<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>



<h2>cloning package #<?php echo $package['Package']['packageId'] . ': ' . $package['Package']['packageName']; ?></h2>

<? if (sizeof($loas) > 0) { ?>
	<br/>please choose the target LOA:<br/><br/>

	<? foreach ($loas as $loaId=>$loaDesc) { ?>
	    <a href="?loa=<?= $loaId; ?>"><?= $loaDesc; ?></a><br/><br/>
	<? } ?>
<? } else { ?>
	<br/>no target LOAs found<br/><br/>
<? } ?>



<!-- CONTAINER FOR OVERLAYS ====================================================================-->

<div id="formContainer" style="display:none;overflow:hidden">
    <iframe id="dynamicForm" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"></iframe>
</div>
