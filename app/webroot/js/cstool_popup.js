/* CS Tool */

function cs_tool_popup() {
	var w = window.open('/calls/popup#cstool', 'cstool', 'left=0,top=0,width=300,height=' + screen.height + ',menubar=no,toolbor=no,location=no,dialog=yes');
	w.moveTo(0, 0);
	w.resizeTo(300, screen.height);
}

function cs_search_popup() {
	var w = window.open('/users#cstool', 'cssearch', 'left=301,top=0,width=' + (screen.width - 300) + ',height=' + screen.height + 'menubar=yes,toolbor=yes,location=yes,dialog=no');
	w.moveTo(301, 0);
	w.resizeTo(screen.width - 300, screen.height);
}

jQuery(function() {
	var $ = jQuery;
	$('body').keypress(function(e) {
		if (e.which == 96) {
			cs_tool_popup();
			e.preventDefault();
		}
	});
});
