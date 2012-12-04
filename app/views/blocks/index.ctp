<style>
	div.title-header {
		padding-bottom: 0;
	}
	#content-area {
		padding-top: 0;
		padding-left: 10px;
	}
	#editorDiv {
		margin-left: 257px;
		margin-right: 16px;
		padding-left: 16px;
		min-height: 400px;
	}
	.editorPanel {
		border-color: #cccccc;
		border-width: 1px;
		border-style: solid;
		min-height: 300px;
		padding: 8px;
		background: white;
		box-shadow: 3px 3px 10px #dddddd;
	}
	#blockTree {
		min-height: 400px;
	}
	#previewFrame {
		margin: 0;
		padding: 0;
		box-shadow: 3px 3px 10px #88bbdd;
		border: none;
		width: 100%;
		height: 400px;
	}
	#blockToolbar {
		background-color: #dddddd;
		margin: 24px 0;
	}
	#blockToolbar a {
		height: 16px;
		text-decoration: none;
		color: black;
		display: inline-block;
		padding: 2px 4px;;
		margin: 4px;
		border-style: solid;
		border-color: #cccccc;
		border-width: 1px;
		border-radius: 4px;
		background: #eeeeee;
			}
	#blockToolbar a:hover {
		background-color: #ffffff;
		color: blue;
	}
	#blockToolbar a img {
		width: 16px;
		height: 16px;
		vertical-align:middle;
		margin-right: 4px;
	}
	#blockToolbar a span {
		font-size: 10px;
		font-weight: bold;
	}
</style>
<?php
$this->pageTitle = 'Blocks Editor';
$this->set('hideSidebar', true);
?>

<div id="blockToolbar"></div>

<div class="clearfix"></div>

<div id="blockTree" class="demo" style="float: left; width: 256px; overflow-x: scroll;"></div>

<div id="editorDiv">
	<div class="editorPanel">
		<div style="text-align: center; font-size: 32px; text-shadow: 5px 5px 25px #888;">
			<pre><?php echo htmlentities(print_r(serialize($z = new BlockDivModule), true)); ?></pre>
		</div>
	</div>
</div>

<div style="clear: both;"></div>

<!--
<br/>
<hr/>
<br/>
<iframe id="previewFrame" src="http://www.luxurylink.com/"></iframe>
-->

<br/>
<br/>

<script type="text/javascript" src="/js/blocks.js"></script>
