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

<script type="text/javascript">
	jQuery(function() {
		var $ = jQuery;
		var $tree = $('#blocktree');
		$tree.jstree({
			"plugins" : ["themes", "html_data", "ui", /*"contextmenu",*/"crrm", "hotkeys", "types", "dnd"],
			"dnd" : {
				"copy_modifier" : "shift"
			},
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
			"types" : {
				"valid_children" : ["page", "image"],
				"types" : {
					"page" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/page.png"
						},
						"valid_children" : ['PhotoModule', 'main']
					},
					"div" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/layout.png",
							"valid_children" : ['div', 'ad']
						}
					},
					"ad" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/television.png",
							"max_depth" : 0,
							"max_children" : 0
						}
					},
					"module" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/script_gear.png"
						}
					},
					"main" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/application_tile_vertical.png",
							"valid_children" : ['content', 'sidebar']
						}
					},
					"content" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/application_view_detail.png"
						}
					},
					"sidebar" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/application_side_expand.png",
							"valid_children" : ['div', 'ad']
						}
					},
					"TabsModule" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/application_cascade.png",
							"valid_children" : ['tab']
						}
					},
					"tab" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/tab.png"
						}
					},
					"image" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/image.png",
							"max_depth" : 0,
							"max_children" : 0
						}
					},
					"PhotoModule" : {
						"icon" : {
							"image" : "http://ui.llsrv.us/images/icons/silk/images.png",
							"valid_children" : ['image'],
							"max_depth" : 1
						}
					},
					"default" : {
						"valid_children" : 'all',
						"max_depth" : -1,
						"max_children" : -1
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
				"theme" : "luxury"
			}
		}).bind("loaded.jstree", function(event, data) {
			$(this).jstree('open_all');
		}).bind("select_node.jstree", function(event, data) {
			var $target = $(data.rslt.obj);
			var type = $target.attr('rel');
			var $output = $('<div class="editorPanel" />');
			$output.append($('<h2/>').text(type.charAt(0).toUpperCase() + type.slice(1)));
			$output.append($('<h3/>').text($target.contents('a').contents().filter(function() {
				return this.nodeType === 3;
			}).text()));
			$('#editorDiv').html($output);
		}).delegate("a", "click", function(event, data) {
			event.preventDefault();
		});
	}); 
</script>
