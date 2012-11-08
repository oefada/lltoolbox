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
		padding-left: 16px;
		border: none;
		border-left-style: solid;
		border-left-width: 1px;
		border-left-color: #EEEEEE;
		min-height: 400px;
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
		<li id="phtml_1">
			<a href="#">Ski &amp; Snow</a>
			<ul>
				<li id="phtml_2">
					<a href="#">PhotoModule</a>
					<ul>
						<li id="phtml_22">
							<a href="#">Image</a>
						</li>
						<li id="phtml_23">
							<a href="#">Image</a>
						</li>
						<li id="phtml_24">
							<a href="#">Image</a>
						</li>
						<li id="phtml_25">
							<a href="#">Image</a>
						</li>
						<li id="phtml_26">
							<a href="#">Image</a>
						</li>
					</ul>
				</li>
				<li id="phtml_3">
					<a href="#">Main</a>
					<ul>
						<li id="phtml_31">
							<a href="#">Content</a>
							<ul>
								<li id="phtml_311">
									<a href="#">TabsModule</a>
									<ul>
										<li id="phtml_3111">
											<a href="#">Colorado</a>
										</li>
										<li id="phtml_3112">
											<a href="#">California</a>
										</li>
										<li id="phtml_3113">
											<a href="#">International</a>
										</li>
										<li id="phtml_3114">
											<a href="#">Canada</a>
										</li>
										<li id="phtml_3115">
											<a href="#">Other</a>
										</li>
									</ul>
								</li>
								<li id="phtml_311">
									<a href="#">SEO Text</a>
								</li>
								<li id="phtml_311">
									<a href="#">Newsletter Signup</a>
								</li>
							</ul>
						</li>
						<li id="phtml_32">
							<a href="#">Sidebar</a>
							<ul>
								<li id="phtml_321">
									<a href="#">Featured Auction</a>
								</li>
								<li id="phtml_322">
									<a href="#">Advertisement</a>
								</li>
								<li id="phtml_323">
									<a href="#">Learn More</a>
								</li>
								<li id="phtml_324">
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
	ASDF
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

<script type="text/javascript">
	jQuery(function() {
		var $ = jQuery;
		$("#blocktree").jstree({
			"plugins" : ["themes", "html_data", "ui", "contextmenu", "crrm", "hotkeys", "types"],
			"core" : {
				"initially_open" : ["phtml_1"]
			},
			"crrm" : {
				"move" : {
					"check_move" : function(m) {
						var p = this._get_parent(m.o);
						if (!p) {
							return false;
						}
						p = (p == -1) ? this.get_container() : p;
						if (p === m.np) {
							return true;
						}
						if (p[0] && m.np[0] && p[0] === m.np[0]) {
							return true;
						}
						return false;
					}
				}
			},
			"contextmenu" : {
				"items" : {
					"add" : {
						// The item label
						"label" : "Add",
						// The function to execute upon a click
						"action" : function(obj) {
							console.log(Math.random(), "addAction", obj);
						},
						"separator_before" : false, // Insert a separator before the item
						"separator_after" : true, // Insert a separator after the item
						"submenu" : {
							"ClientDisplayModule" : {
								"label" : "ClientDisplayModule"
							},
							"DivModule" : {
								"label" : "DivModule"
							},
							"HeaderModule" : {
								"label" : "HeaderModule"
							},
							"LinkModule" : {
								"label" : "LinkModule"
							},
							"ParagraphModule" : {
								"label" : "ParagraphModule"
							},
							"PhotoModule" : {
								"label" : "PhotoModule"
							}
						}
					}
				}
			},
			"themes" : {
				"theme" : "classic"
			}
		}).bind("loaded.jstree", function(event, data) {
		}).bind("select_node.jstree", function(event, data) {
			console.log(Math.random(), event, data, data.rslt.obj.attr("id"));
			var outputText = typeof data.rslt.obj.attr("showme");
			if ( typeof outputText == 'string' && outputText.length > 0) {
				$('#editorDiv').html(outputText);
			}
		}).delegate("a", "click", function(event, data) {
			event.preventDefault();
		});
		setTimeout(function() {
			$.jstree._reference("#phtml_1").open_node("#phtml_1");
		}, 1000);
		setTimeout(function() {
			$.jstree._reference("#phtml_2").open_node("#phtml_2");
		}, 2000);
		setTimeout(function() {
			$.jstree._reference("#phtml_3").open_node("#phtml_3");
		}, 3000);
		setTimeout(function() {
			$.jstree._reference("#phtml_31").open_node("#phtml_31");
		}, 4000);
		setTimeout(function() {
			$.jstree._reference("#phtml_311").open_node("#phtml_311");
		}, 5000);
		setTimeout(function() {
			$.jstree._reference("#phtml_32").open_node("#phtml_32");
		}, 6000);
	}); 
</script>
