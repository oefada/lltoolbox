<div style="font-size: 300%;">Searching for: <b><?php echo $search; ?></b></div>
<br/>
<div>
	<?php echo $html->image('loading_bar.gif'); ?>
</div>
<br/>

<script type="text/javascript">
	jQuery(function() {
		setTimeout(function() {
			window.location.replace('/calls/search?q=<?php echo urlencode($search); ?>');
		}, 5);
	}); 
</script>
