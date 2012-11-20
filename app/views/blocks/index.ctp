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
	#blocktree {
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
</style>
<?php
$this->pageTitle = 'Blocks Editor';
$this->set('hideSidebar', true);
?>

<div id="blocktree" class="demo" style="float: left; width: 256px; overflow-x: scroll;">
	<ul>
		<li id="phtml_1" rel="page">
			<a href="#">Ski &amp; Snow</a>
			<ul>
				<li id="phtml_2" rel="PhotoModule">
					<a href="#">PhotoModule</a>
					<ul>
						<li id="phtml_22" rel="image">
							<a href="#">Image 1</a>
						</li>
						<li id="phtml_23" rel="image">
							<a href="#">Image 2</a>
						</li>
						<li id="phtml_24" rel="image">
							<a href="#">Image 3</a>
						</li>
						<li id="phtml_25" rel="image">
							<a href="#">Image 4</a>
						</li>
						<li id="phtml_26" rel="image">
							<a href="#">Image 5</a>
						</li>
					</ul>
				</li>
				<li id="phtml_3" rel="main">
					<a href="#">Main</a>
					<ul>
						<li id="phtml_31" rel="content">
							<a href="#">Content</a>
							<ul>
								<li id="phtml_311" rel="TabsModule">
									<a href="#">TabsModule</a>
									<ul>
										<li id="phtml_3111" rel="tab">
											<a href="#">Colorado</a>
										</li>
										<li id="phtml_3112" rel="tab">
											<a href="#">California</a>
										</li>
										<li id="phtml_3113" rel="tab">
											<a href="#">International</a>
										</li>
										<li id="phtml_3114" rel="tab">
											<a href="#">Canada</a>
										</li>
										<li id="phtml_3115" rel="tab">
											<a href="#">Other</a>
										</li>
									</ul>
								</li>
								<li id="phtml_311" rel="div">
									<a href="#">SEO Text</a>
								</li>
								<li id="phtml_311" rel="div">
									<a href="#">Newsletter Signup</a>
								</li>
							</ul>
						</li>
						<li id="phtml_32" rel="sidebar">
							<a href="#">Sidebar</a>
							<ul>
								<li id="phtml_321" rel="module">
									<a href="#">Featured Auction</a>
								</li>
								<li id="phtml_322" rel="ad">
									<a href="#">Advertisement</a>
								</li>
								<li id="phtml_323" rel="module">
									<a href="#">Learn More</a>
								</li>
								<li id="phtml_324" rel="ad">
									<a href="#">Advertisement</a>
								</li>
							</ul>
						</li>
					</ul>
				</li>
			</ul>
		</li>
	</ul>
</div>

<div id="editorDiv">
	<div class="editorPanel">
		<div style="text-align: center; font-size: 32px; text-shadow: 5px 5px 25px #888;">
			Select a block to edit from the tree on the left.
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
