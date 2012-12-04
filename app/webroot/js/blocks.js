jQuery(function() {
	var $ = jQuery;

	// Add module toolbar
	$('#blockToolbar').on('click', 'a', function(e) {
		e.preventDefault();
		var $target = $('#blockTree > ul > li').length == 0 ? -1 : $('#blockTree').jstree('get_selected');
		var $newNode = $('#blockTree').jstree('create', $target, 'last', {
			'data' : $(this).attr('rel'),
			'attr' : {
				'rel' : $(this).attr('rel')
			}
		}, null, true);
		$('#blockTree').jstree('deselect_all').jstree('select_node', $newNode);
	});

	var typeData = {
		'BlockPageModule' : {
			'toolbarName' : 'Page',
			'parameters' : {
				'meta_title' : 'text',
				'meta_description' : 'text',
				'meta_keywords' : 'text'
			},
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/page.png'
			},
			'valid_children' : ['BlockDivModule', 'BlockPhotoModule']
		},
		'BlockDivModule' : {
			'toolbarName' : 'Div',
			"icon" : {
				"image" : "http://ui.llsrv.us/images/icons/silk/layout.png"
			},
			"valid_children" : ['BlockDivModule', 'BlockHeaderModule', 'BlockParagraphModule', 'BlockPhotoModule', 'BlockTabsModule', 'BlockLinkModule']
		},
		'BlockHeaderModule' : {
			'toolbarName' : 'Header',
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/text_heading_1.png'
			},
			'valid_children' : 'none'
		},
		'BlockParagraphModule' : {
			'toolbarName' : 'Paragraph',
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/text_dropcaps.png'
			},
			'valid_children' : "BlockLinkModule"
		},
		'BlockLinkModule' : {
			'toolbarName' : 'Link',
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/page_white_world.png'
			},
			'valid_children' : 'none'
		},
		'BlockPhotoModule' : {
			'toolbarName' : 'PhotoModule',
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/images.png'
			},
			'valid_children' : ['BlockImageModule']
		},
		'BlockImageModule' : {
			'toolbarName' : 'Image',
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/image.png'
			},
			'valid_children' : 'none'
		},
		'BlockTabsModule' : {
			'toolbarName' : 'Tabs',
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/application_cascade.png'
			},
			'valid_children' : ['BlockTabModule']
		},
		'BlockTabModule' : {
			'toolbarName' : 'Tab',
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/tab.png'
			},
			'valid_children' : ['BlockDivModule']
		},
		'default' : {
			'valid_children' : 'none',
			'max_depth' : -1,
			'max_children' : -1
		}
	};

	// Build toolbar buttons
	for (var key in typeData) {
		var obj = typeData[key];
		if ( typeof obj.toolbarName == 'string') {
			var $newLink = $('<a href="#" />');
			if ( typeof obj.icon == 'object' && typeof obj.icon.image == 'string') {
				$newLink.append($('<img />').attr('src', obj.icon.image));
			}
			$newLink.append($('<span/>').text(obj.toolbarName));
			$newLink.attr('rel', key);
			$('#blockToolbar').append($newLink);
		}
	}

	// Highlight enabled toolbar buttons
	var updateToolbarButtons = function(activeModule) {
		$('#blockToolbar a').removeClass('active');
		var $selected = $('#blockTree').jstree('get_selected');
		if ($('#blockTree > ul > li').length > 0) {
			$selected.each(function(i) {
				var moduleType = $(this).attr('rel');
				if ( typeof moduleType == 'string') {
					if ( typeof typeData[moduleType] == "object" && typeof typeData[moduleType]['valid_children'] == "object") {
						var valid_children = typeData[moduleType]['valid_children'];
						for (var child in valid_children) {
							if (valid_children.hasOwnProperty(child)) {
								$('#blockToolbar a[rel="' + valid_children[child] + '"]').addClass('active');
							}
						}
					}
				}
			});
		} else {
			$('#blockToolbar a[rel="BlockPageModule"]').addClass('active');
		}
	};
	updateToolbarButtons();

	var loadEditor = function($target) {
		var $editor = $('#editorDiv');
		var $panel = $('<div class="editorPanel"></div>');
		if ($target == 'boot') {
			$panel.append('Welcome to Blocks!');
			$panel.css({
				'text-align' : 'center',
				'font-size' : '32px',
				'text-shadow' : '5px 5px 25px #888',
				'color' : '#666',
				'padding-top' : '24px'
			});
		} else {
			$panel.append('<h2>Editor</h2>');
		}
		$editor.empty().append($panel);
	};
	loadEditor('boot');

	var generateData = function() {
		var data = $('#blockTree').jstree('get_json', -1);
		var json = JSON.stringify(data, null, ' ');
		data = (JSON.parse(json));
		$('#dataDiv').text(data);
	};

	var $tree = $('#blockTree');
	$tree.jstree({
		"plugins" : ["themes", "html_data", "ui", "crrm", "hotkeys", "types", "dnd", 'json_data'],
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

		'types' : {
			'valid_children' : ['BlockPageModule'],
			'max_children' : 1,
			'types' : typeData
		},

		"themes" : {
			"theme" : "luxury"
		}
	}).bind("loaded.jstree", function(event, data) {
		$(this).jstree('open_all');
	}).bind('deselect_node.jstree', function(event, data) {
		updateToolbarButtons(null);
		loadEditor(null);
		generateData();
	}).bind("select_node.jstree", function(event, data) {
		var $target = $(data.rslt.obj);
		var type = $target.attr('rel');
		updateToolbarButtons(type);
		loadEditor($target);
		generateData();
	}).delegate("a", "click", function(event, data) {
		event.preventDefault();
	});
});
