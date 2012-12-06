jQuery(function() {
	var $ = jQuery;

	if ( typeof JSON != 'object' || typeof JSON.stringify != 'function') {
		alert('Your browser is not supported.');
	}

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
				'meta_title' : 'input',
				'meta_description' : 'input',
				'meta_keywords' : 'input'
			},
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/page.png'
			},
			'valid_children' : ['BlockLayoutModule']
		},
		'BlockLayoutModule' : {
			'toolbarName' : 'Layout',
			'parameters' : {
				'class' : {
					'option' : {
						'full' : 'Full Width',
						'content' : 'Content',
						'sidebar' : 'Sidebar'
					}
				}
			},
			"icon" : {
				"image" : "http://ui.llsrv.us/images/icons/silk/application_side_boxes.png"
			},
			"valid_children" : ['BlockPhotoModule', 'BlockDivModule', 'BlockHeaderModule', 'BlockParagraphModule', 'BlockPhotoModule', 'BlockTabsModule', 'BlockLinkModule', 'BlockPrefabModule', 'BlockAdvertisingModule']
		},
		'BlockDivModule' : {
			'toolbarName' : 'Div',
			'parameters' : {
				'content' : 'textarea',
				'class' : 'input'
			},
			"icon" : {
				"image" : "http://ui.llsrv.us/images/icons/silk/layout.png"
			},
			"valid_children" : ['BlockDivModule', 'BlockHeaderModule', 'BlockParagraphModule', 'BlockPhotoModule', 'BlockTabsModule', 'BlockLinkModule', 'BlockPrefabModule', 'BlockAdvertisingModule', 'BlockClientDisplayModule']
		},
		'BlockHeaderModule' : {
			'toolbarName' : 'Header',
			'parameters' : {
				'content' : 'input',
				'level' : {
					'option' : {
						'1' : '1',
						'2' : '2',
						'3' : '3',
						'4' : '4',
						'5' : '5',
						'6' : '6'
					}
				}
			},
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/text_heading_1.png'
			},
			'valid_children' : 'none'
		},
		'BlockParagraphModule' : {
			'toolbarName' : 'Paragraph',
			'parameters' : {
				'content' : 'textarea'
			},
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/text_dropcaps.png'
			},
			'valid_children' : "none"
		},
		'BlockLinkModule' : {
			'toolbarName' : 'Link',
			'parameters' : {
				'content' : 'input',
				'href' : 'input',
				'clicktrack' : 'input'
			},
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
			'parameters' : {
				'src' : 'input',
				'link' : 'input'
			},
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
			'parameters' : {
				'title' : 'input'
			},
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/tab.png'
			},
			'valid_children' : ['BlockDivModule']
		},
		'BlockAdvertisingModule' : {
			'toolbarName' : 'Advertising',
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/television.png'
			},
			'valid_children' : 'none',
		},
		'BlockClientDisplayModule' : {
			'toolbarName' : 'Client Display',
			'parameters' : {
				'clientIds' : 'textarea',
				'themeIds' : 'textarea',
				'urls' : 'textarea',
			},
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/status_online.png'
			},
			'valid_children' : 'none',
		},
		'BlockPrefabModule' : {
			'toolbarName' : 'Prefab',
			'parameters' : {
				'type' : {
					'option' : {
						'CommunityModule' : 'Community',
						'NewsletterModule' : 'Newsletter Signup Box',
						'FeaturedAuctionsModule' : 'Featured Auctions'
					}
				},
			},
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/plugin.png'
			},
			'valid_children' : 'none',
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
			var rel = $target.attr('rel');
			if ( typeof rel == 'string' && typeData.hasOwnProperty(rel)) {
				var type = typeData[rel];
				$panel.append('<h1>' + rel + ' Editor</h1>');
				if (type.hasOwnProperty('parameters')) {
					for (var parameter in type['parameters']) {
						if (type['parameters'].hasOwnProperty(parameter)) {
							var $newParam = $('<div class="editorParameter" />');
							$newParam.append($('<h2/>').text(parameter));
							var $newInput = $('');
							switch(type['parameters'][parameter]) {
								case 'input':
									$newInput = $('<input type="text" />');
									$newInput.attr('name', parameter);
									$newParam.append($newInput);
									break;
								case'textarea':
									$newInput = $('<textarea />');
									$newInput.attr('name', parameter);
									$newParam.append($newInput);
									break;
								default:
									if ( typeof type['parameters'][parameter] == 'object') {
										if (type['parameters'][parameter].hasOwnProperty('option')) {
											for (var opt in type['parameters'][parameter]['option']) {
												if (type['parameters'][parameter]['option'].hasOwnProperty(opt)) {
													$newInput = $('<input />');
													$newInput.attr({
														'type' : 'radio',
														'name' : parameter,
														'value' : type['parameters'][parameter]['option'][opt]
													});
													$newParam.append($newInput);
													$newParam.append($('<span/>').text(type['parameters'][parameter]['option'][opt]));
													$newParam.append('<br/>');
												}
											}
										} else {
											$newParam.append('???');
										}
									}
							}
							$panel.append($newParam);
						}
					}
				}
			}
		}
		$editor.empty().append($panel);
	};
	loadEditor('boot');

	var generateData = function() {
		var data = $('#blockTree').jstree('get_json', -1, ['data-blocks'], []);
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
