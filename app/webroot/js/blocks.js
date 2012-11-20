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
