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
			"valid_children" : ['BlockDivModule', 'BlockHeaderModule', 'BlockParagraphModule', 'BlockPhotoModule', 'BlockTabsModule','BlockLinkModule']
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
			'toolbarName' : 'Paragraph',
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/text_dropcaps.png'
			},
			'valid_children' : "BlockLinkModule"
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

	var $tree = $('#blockTree');
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
