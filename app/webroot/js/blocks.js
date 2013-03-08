/*jshint
 forin:true,
 noarg:true,
 noempty:true,
 eqeqeq:true,
 bitwise:true,
 strict:true,
 undef:true,
 unused:true,
 curly:true,
 browser:true,
 evil:true,
 devel:true,
 jquery:true,
 sub:true
 */
jQuery(function() {
	var $ = jQuery;

	var $editPointer = null;

	if ( typeof JSON != 'object' || typeof JSON.stringify != 'function') {
		alert('Your browser is not supported.');
	}

	$('a[href="#top"]').on('click', function(e) {
		e.preventDefault();
		$('body').animate({
			scrollTop : 0
		}, 500);
	});

	// Handle toolbar button click
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

	// Ping function
	var pingServer = function(urls, $dialog) {
		var url = urls.shift();
		if (url) {
			$dialog.append($('<p/>').text(url.replace(/\?clearCache.*$/, '')).prepend($('<img/>').attr('src', url).css({
				'width' : '8px',
				'height' : '8px',
				'background-color' : 'red',
				'margin-right' : '8px'
			}).on('error', function(e) {
				$(this).css('background-color', 'green');
			})));
			pingServer(urls, $dialog);
		}
	};

	// Save data
	$('div.pressMe').on('click', 'a', function(e) {
		e.preventDefault();
		var data = {};
		switch ($(this).attr('href')) {
			case '#publish':
				data['publish'] = true;
			case '#save':
				var json = JSON.stringify($('#blockTree').jstree('get_json', -1), null, ' ').replace('</script>', '<\\\\/script>');
				data['treeData'] = JSON.parse(json);
				$.ajax({
					'type' : 'POST',
					'cache' : false,
					'data' : data,
					'dataType' : 'html',
					'complete' : function(d, t, j) {
						var previewUrl = d.getResponseHeader('X-Blocks-Preview');
						if (d.getResponseHeader('X-Blocks-Publish')) {
							var previewUrl = d.getResponseHeader('X-Blocks-Publish');
						}
						if (previewUrl) {
							$('#previewFrame').attr('src', previewUrl);
							$('body').animate({
								scrollTop : $("#previewDiv").offset().top
							}, 500, null, function() {
								if ( typeof data['publish'] == 'boolean' && data['publish'] == true) {
									var $dialog = $('<div/>');
									$dialog.append($('<h3/>').text('Pinging servers:'));
									if (window.location.href.toLowerCase().indexOf('toolboxdev') != -1) {
										pingServer([d.getResponseHeader('X-Blocks-Publish')], $dialog);
									} else {
										var baseUrl = d.getResponseHeader('X-Blocks-Publish');
										var urls = [];
										for (var i = 0; i <= 9; i++) {
											urls.push(baseUrl.replace(/^[^\.]+\./, 'http://www' + i + '.'));
										}
										pingServer(urls, $dialog);
									}
									$dialog.dialog({
										'title' : 'Publish',
										'draggable' : false,
										'resizable' : false,
										'width' : 640,
										'minHeight' : 300,
										'modal' : true
									});
									setTimeout(function() {
										$dialog.dialog('destroy');
									}, 6000);
								}
							});
							previewUrl = previewUrl.replace(/\?clearCache.*$/, '');
							$('#previewLink').empty().append($('<a/>').attr('href', previewUrl).attr('target', 'blockPreview').text(previewUrl));
						}
					}
				});
				$(this).effect('highlight');
				break;
		}
	});

	var typeData = {
		'BlockPageModule' : {
			'toolbarName' : 'Page',
			'titleField' : 'meta_title',
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
			'titleField' : 'class',
			'parameters' : {
				'class' : {
					'option' : {
						'full' : 'Full Width',
						'content' : 'Content',
						'sidebar' : 'Sidebar',
						'block' : 'Block'
					}
				}
			},
			"icon" : {
				"image" : "http://ui.llsrv.us/images/icons/silk/layout.png"
			},
			"valid_children" : ['BlockDivModule', 'BlockPrefabModule', 'BlockAdvertisingModule']
		},
		'BlockSectionsModule' : {
			'toolbarName' : 'Sections',
			'titleField' : 'description',
			'parameters' : {
				'description' : 'input'
			},
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/bricks.png'
			},
			'valid_children' : ['BlockSectionModule']
		},
		'BlockSectionModule' : {
			'toolbarName' : 'Section',
			'titleField' : 'keyName',
			'parameters' : {
				'keyName' : 'input'
			},
			"icon" : {
				"image" : "http://ui.llsrv.us/images/icons/silk/brick.png"
			},
			"valid_children" : ['BlockDivModule']
		},
		'BlockDivModule' : {
			'toolbarName' : 'Div',
			'titleField' : 'content',
			'parameters' : {
				'content' : 'textarea',
				'class' : 'input'
			},
			"icon" : {
				"image" : "http://ui.llsrv.us/images/icons/silk/page_white_width.png"
			},
			"valid_children" : ['BlockDivModule', 'BlockHeaderModule', 'BlockParagraphModule', 'BlockPhotoModule', 'BlockTabsModule', 'BlockLinkModule', 'BlockPrefabModule', 'BlockAdvertisingModule', 'BlockClientDisplayModule']
		},
		'BlockHeaderModule' : {
			'toolbarName' : 'Header',
			'titleField' : 'content',
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
			'titleField' : 'content',
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
			'titleField' : 'content',
			'parameters' : {
				'content' : 'input',
				'href' : 'input',
				'clicktrack' : 'input',
				'class' : 'input'
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
			'titleField' : function(data) {
				var title = '';
				if ( typeof data.title == 'string' && data.title.length > 0) {
					title = data.title;
				} else {
					if ( typeof data.src == 'string') {
						title = data.src
						if (title.length > 17) {
							title = '...' + title.substr(-17);
						}
					}
				}
				return title;
			},
			'parameters' : {
				'src' : 'input',
				'linkHref' : 'input',
				'linkRel' : 'input',
				'linkClicktrack' : 'input',
				'title' : 'input'
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
			'titleField' : 'title',
			'parameters' : {
				'title' : 'input',
				'linkBoxText' : 'input',
				'linkBoxHref' : 'input',
				'linkBoxClicktrack' : 'input',
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
				'maxClientsToDisplay' : 'input',
			},
			'icon' : {
				'image' : 'http://ui.llsrv.us/images/icons/silk/status_online.png'
			},
			'valid_children' : 'none',
		},
		'BlockPrefabModule' : {
			'toolbarName' : 'Prefab',
			'titleField' : 'type',
			'parameters' : {
				'type' : {
					'option' : {
						'CommunityModule' : 'Community',
						'NewsletterModuleSidebar' : 'Newsletter Signup Box for Sidebar',
						'NewsletterModuleContent' : 'Newsletter Signup Box for wide Content area',
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
			$('#blockToolbar a[rel="BlockPageModule"], #blockToolbar a[rel="BlockSectionsModule"]').addClass('active');
		}
	};
	updateToolbarButtons();

	var loadEditor = function($target) {
		var $editor = $('#editorDiv');
		var $panel = $('<div class="editorPanel"></div>');
		if ($target == 'boot' || $target == null) {
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
					var data = $target.data();
					for (var parameter in type['parameters']) {
						if (type['parameters'].hasOwnProperty(parameter)) {
							var $newParam = $('<div class="editorParameter" />');
							$newParam.append($('<h2/>').text(parameter));
							var $newInput = $('');
							switch(type['parameters'][parameter]) {
								case 'input':
									$newInput = $('<input type="text" />');
									$newInput.attr('name', parameter);
									if (data.hasOwnProperty(parameter)) {
										$newInput.val(data[parameter]);
									}
									$newParam.append($newInput);
									break;
								case'textarea':
									$newInput = $('<textarea />');
									$newInput.attr('name', parameter);
									if (data.hasOwnProperty(parameter)) {
										$newInput.val(data[parameter]);
									}
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
														'value' : opt
													});
													if (data.hasOwnProperty(parameter)) {
														if (data[parameter] == opt) {
															$newInput.attr('checked', true);
														}
													}
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
			$panel.append('<input type="button" class="doNotPress" value="Update" title="Press this button after making changes above." />');
			if ($panel.find('textarea,input[type="text"]').length > 0) {
				$panel.append('<input type="button" class="htmlValidate" value="Validate" title="Look for problems in the HTML" />');
				$panel.append('<input type="button" class="htmlTidy" value="Tidy" title="Tidy up the HTML" />');
			}
			$panel.append($('<br/><br/><div class="blocksHelp"/>').html('&#160;'));
			$.ajax({
				'url' : '/blocks/help/module:' + rel,
				'dataType' : 'html',
				'cache' : true,
				'type' : 'POST',
				'success' : function(data, textStatus, jqXHR) {
					$('#editorDiv div.blocksHelp').html(data);
				}
			});
		}
		$editor.empty().append($panel);
	};
	loadEditor('boot');

	var handleChange = function() {
		var $selected = $('#blockTree').jstree('get_selected');
		var data = {};
		$selected.removeData();
		$('#editorDiv *[name]').each(function(i) {
			if ($(this).attr('name')) {
				if ($(this).attr('type') == 'radio') {
					if ($(this).attr('checked')) {
						$selected.data($(this).attr('name'), $(this).val());
					}
				} else {
					var val = $(this).val();
					if ( typeof val == 'string' && val.length > 0) {
						$selected.data($(this).attr('name'), val);
					}
				}
			}
		});
		data = $selected.data();
		var rel = $selected.attr('rel')
		if ( typeof rel == 'string') {
			if (typeData.hasOwnProperty(rel) && typeData[rel].hasOwnProperty('titleField')) {
				var titleField = typeData[rel]['titleField'];
				var titleText = '???';
				switch(typeof titleField) {
					case 'string':
						if ( typeof data[titleField] == 'string') {
							titleText = data[titleField];
						} else {
							titleText = rel;
						}
						break;
					case 'function':
						titleText = titleField(data);
						break;
					default:
						titleText = rel;
				}
				if (titleText.length > 20) {
					titleText = titleText.substring(0, 20) + '...';
				}
				$('#blockTree').jstree('rename_node', $selected, titleText);

			}
		}
		$('#dataDebug').text(JSON.stringify($('#blockTree').jstree('get_json', -1, [], []), null, ' '));
		$('input[type="button"].doNotPress').css('color', 'red');
	};

	var loadTree = function(json_data) {
		var loadData = [{}];
		if ( typeof json_data == 'object') {
			loadData = json_data;
		}
		var $tree = $('#blockTree');
		$tree.empty();
		$tree.jstree({
			"plugins" : ["themes", "ui", "crrm", "hotkeys", "types", "dnd", 'json_data'],
			'json_data' : {
				'data' : loadData,
			},
			"dnd" : {
				"copy_modifier" : "shift"
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
				'valid_children' : ['BlockPageModule', 'BlockSectionsModule'],
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
		}).bind("select_node.jstree", function(event, data) {
			$editPointer = $(data.rslt.obj);
			var type = $editPointer.attr('rel');
			updateToolbarButtons(type);
			loadEditor($editPointer);
		}).delegate("a", "click", function(event, data) {
			event.preventDefault();
		});
	};
	if ( typeof window.editorLoadData != 'undefined') {
		loadTree(window.editorLoadData);
	} else {
		loadTree();
	}
	$('#editorDiv').on('change', 'input,textarea', handleChange).on('click', 'input[type="radio"]', handleChange).on('keyup', 'input,textarea', handleChange).on('keypress', 'input,textarea', handleChange);

	$('#editorDiv').on('click', 'input[type="button"].doNotPress', function(e) {
		$(this).css('color', 'green');
	});

	var phpTidy = function() {
		$('#editorDiv').find('.tidyError').remove();
		var $tidyUp = $(this).hasClass('htmlTidy');
		var validateData = {};
		$('#editorDiv').find('textarea,input[type="text"]').each(function(i) {
			$(this).css('background-color', '#ffffee');
			if ($(this).attr('name')) {
				validateData[$(this).attr('name')] = $(this).val();
			}
		});
		$.ajax({
			'url' : '/blocks/tidy',
			'data' : {
				'validate' : validateData
			},
			'dataType' : 'json',
			'cache' : false,
			'type' : 'POST',
			'success' : function(data, textStatus, jqXHR) {
				if (data.cleanroom) {
					for (var x in data.cleanroom) {
						if (data.cleanroom.hasOwnProperty(x)) {
							$('#editorDiv').find('textarea,input[type="text"]').each(function(i) {
								if ($(this).attr('name') == x) {
									if (data.cleanroom[x].background) {
										$(this).css('background', data.cleanroom[x].background);
									}
									if ($tidyUp) {
										if (data.cleanroom[x].tidied) {
											$(this).val(data.cleanroom[x].tidied);
										}
									}
									if (data.cleanroom[x].error) {
										var $newError = $('<pre class="tidyError"/>').text(data.cleanroom[x].error);
										$newError.html($newError.html().replace(/line ([0-9]+) column ([0-9]+)/g, function(match, contents, offset, s) {
											return match;
											return '<a class="lineJump" href="#">' + match + '</a>';
										}));
										$(this).after($newError);
									}
								}
							});
						}
					}
				}
			}
		});
	};

	$('#editorDiv').on('click', 'input[type="button"].htmlValidate', phpTidy);
	$('#editorDiv').on('click', 'input[type="button"].htmlTidy', phpTidy);

	$('#editorDiv').on('click', 'a.lineJump', function(e) {
		var $target = $(this).parent().prev();
		$target.effect('highlight');
		var n = $(this).text().split(' ');
		var nLine = n[1];
		var nCol = n[3];
	});

	var link = document.createElement('link');
	link.type = 'image/x-icon';
	link.rel = 'shortcut icon';
	link.href = 'http://ui.llsrv.us/images/icons/silk/brick.png';
	document.getElementsByTagName('head')[0].appendChild(link);

	// Keep alive
	setInterval(function() {
		$.ajax({
			'url' : '/blocks/ping',
			'type' : 'GET',
			'cache' : false,
			'dataType' : 'json',
			'success' : function(data, textStatus, jqXHR) {
			}
		});
	}, 5 * 60 * 1000);
});
