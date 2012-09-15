var $ = jQuery;
var cs_util = {
	tick : function() {
		cs_util.hashCheck();
	},
	hashCheck : function() {
		if (cs_util.oldHash != window.location.hash) {
			if (window.location.hash.match(/^#[A-Za-z]+~[0-9]+$/)) {
				var h = window.location.hash.replace(/^#/, '').split('~');
				if ( typeof h[0] == 'string' && typeof h[1] == 'string') {
					cs_util.doAjax(h[0], h[1]);
				}
			}
			cs_util.oldHash = window.location.hash;
		}
	},
	doAjax : function(thing, value) {
		var data = {
			'url' : '/calls/ajax.json',
			'type' : 'GET',
			'cache' : false,
			'data' : {
				'thing' : thing,
				'value' : value
			},
			'dataType' : 'json',
			'success' : function(d, t, j) {
				cs_util.processAjax(d);
			}
		};
		$.ajax(data);
	},
	processAjax : function(d) {
		for (var prop in d) {
			var o = '';
			o += prop + ': ';
			if ( typeof d[prop] == 'object') {
				o += JSON.stringify(d[prop]);
			} else {
				o += d[prop];
			}
		}
		if ( typeof d.User == 'object' && typeof d.User.userId == 'string') {
			cs_util.updateField('#CallUserId', d.User.userId, d.User.name);
		}
		if ( typeof d.Client == 'object' && typeof d.Client.clientId == 'string') {
			cs_util.updateField('#CallClientId', d.Client.clientId, d.Client.name);
		}
		if ( typeof d.Ticket == 'object' && typeof d.Ticket.ticketId == 'string') {
			cs_util.updateField('#CallTicketId', d.Ticket.ticketId, d.Ticket.ticketId);
		}
	},
	updateField : function(target, value, label) {
		var $t = $(target);
		if ($t.length > 0) {
			if ( typeof value == 'string') {
				$t.val(value).effect('highlight', {
					'color' : '#ccffee'
				}, 1200);
			}
			if ( typeof label == 'string') {
				console.log(Math.random(), label);
				var $s = $t.parent().find('span');
				if ($s.length == 0) {
					$t.parent().find('input').after( $s = $('<span/>'));
				}
				$s.text(label)
			}
		}
	},
	doFormSubmit : function(e) {
		var that = $(e.target);
		that.parents('div.interaction').css({
			'background' : '#eeffee'
		});
	},
	doOmniSearch : function(s) {
		var $cs = $('#CallSearch');
		$cs.val(s);
		$cs.effect('highlight', {
			'color' : '#ccffcc'
		}, 300, function() {
			$cs.effect('highlight', {
				'color' : '#ccffcc'
			}, 1700)
		});
		$cs.parents('form').submit();
	},
	oldHash : ''
};

var cs_clock = {
	dots : false,
	tick : function() {
		if ( typeof cs_util.tick == 'function') {
			cs_util.tick();
		}
		cs_clock.dots = !cs_clock.dots;
		var dots = cs_clock.dots ? ':' : '<span style="color:#777;">:</span>';
		var d = new Date();
		var ds = '<span style="color:#777;">' + d.toDateString() + '</span>';
		ds += ' ';
		ds += (d.getHours() % 12);
		ds += dots;
		ds += (d.getMinutes() < 10 ? '0' : '' ) + d.getMinutes();
		ds += dots;
		ds += (d.getSeconds() < 10 ? '0' : '' ) + d.getSeconds();
		ds += ' ';
		ds += d.getHours() < 12 ? 'AM' : 'PM';
		$('#csToolClock').html(ds);
	}
};

$(function() {
	cs_clock.tick();
	$('#newCs').click(function(e) {
		window.location.replace('/calls/popup');
		e.preventDefault();
	});
	$('#openCsSearch').click(function(e) {
		cs_search_popup();
		e.preventDefault();
	});
	var unsavedChanges = function(e) {
		var that = $(e.target);
		that.parents('div.interaction').css({
			'background' : '#ffffee'
		});
	};
	$('input,textarea,select').change(unsavedChanges).keypress(unsavedChanges);
	$('#CallPopupForm').submit(cs_util.doFormSubmit);
	$('#CallOmniboxForm').submit(function(e) {
		cs_search_popup();
	});

	$('form#CallPopupForm input[id="CallUserId"], form#CallPopupForm input[id="CallClientId"]').change(function(e) {
		var $tid = $(this).attr('id');
		var $for = $('label[for="' + $tid + '"]').text();
		if (isNaN($(this).val() - 0)) {
			if ( typeof $tid == 'string') {
				cs_util.doOmniSearch('~' + $for + ' ' + $(this).val());
			}
			$(this).val('');
		} else if ( typeof $for == 'string') {
			cs_util.doAjax($for.toLowerCase() + 's', $(this).val());
		}
	});
	$('form#CallPopupForm input[type="text"], form#CallPopupForm select').keypress(function(e) {
		if (e.which == 10 || e.which == 13) {
			e.preventDefault();
		}
	});
	$(document).ajaxStart(function(e) {
		$('.ajaxLoadingIndicator').css('visibility', 'visible');
	}).ajaxStop(function(e) {
		$('.ajaxLoadingIndicator').css('visibility', 'hidden');
	});
	window.setInterval(cs_clock.tick, 500);
	$('#CallSearch').focus().select().focus(function(e) {
		setTimeout(function() {
			$('#CallSearch').select();
		}, 1);
	});
});
