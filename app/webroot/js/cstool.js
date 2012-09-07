var cs_util = {
	tick : function() {
		cs_util.hashCheck();
	},
	hashCheck : function() {
		var $ = jQuery;
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
		var $ = jQuery;
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
		console.log('processAjax', Math.random(), d);
	},
	oldHash : ''
};

var cs_clock = {
	dots : false,
	tick : function() {
		if ( typeof cs_util.tick == 'function') {
			cs_util.tick();
		}
		var $ = jQuery;
		cs_clock.dots = !cs_clock.dots;
		var dots = cs_clock.dots ? ':' : '<span style="color:#666;">:</span>';
		var d = new Date();
		var ds = '<span style="color:#666;">' + d.toDateString() + '</span>';
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
	var $ = jQuery;
	cs_clock.tick();
	$('#newCs').click(function(e) {
		window.location.replace('/calls/popup');
		e.preventDefault();
	});
	$('#openCsSearch').click(function(e) {
		cs_search_popup();
		e.preventDefault();
	});
	window.setInterval(cs_clock.tick, 500);
});
