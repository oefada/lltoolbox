<div style="float: right;">
	Drag this to your bookmarks toolbar: <a href="javascript:LL.ls('/inc/js/nile_guide_toolbox.js?rand='+new%20Date().getTime());" style="background: #eeeeee; border-style: solid; border-color: blue; border-width: 3px; border-radius: 8px; color: blue; padding: 5px; font-weight: bold; text-decoration: none; cursor: move;">Nile Guide Toolbox</a>
</div>
<h2>Nile Guide</h2>
<ul>
	<li><?php echo $html->link('Trips',array('action'=>'trips')); ?></li>
</ul>
<br/>
<script type="text/javascript">
	jQuery('a[href^="javascript:LL.ls"]').click(function(e){
		e.preventDefault();
		alert('Drag this link to your Bookmarks toolbar');
	});
</script>
