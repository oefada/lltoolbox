var cs_clock = {
	dots : false,
	tick : function() {
		var $ = jQuery;
		cs_clock.dots = !cs_clock.dots;
		var dots = cs_clock.dots ? ':' : '<span style="color:#666;">:</span>';
		var d = new Date();
		var ds = '<span style="color:#666;">'+d.toDateString()+'</span>';
		ds += ' ';
		ds += (d.getHours() % 12);
		ds += dots;
		ds += (d.getMinutes() < 10 ? '0' : '' ) + d.getMinutes();
		ds += dots;
		ds += (d.getSeconds() < 10 ? '0' : '' ) + d.getSeconds();
		ds += ' ';
		ds += d.getHours() < 12 ? 'PM' : 'AM';
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
