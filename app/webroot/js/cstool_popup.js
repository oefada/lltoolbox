/* CS Tool */

function cs_tool_popup(hash) {
	if ( typeof hash == 'undefined') {
		var u = window.location.pathname.split('/');
		if ( typeof u[2] == 'string' && (u[2] == 'edit' || u[2] == 'view')) {
			hash = (u[1] + '~' + u[3]).replace(/[^A-Za-z0-9\~]/g, '');
		} else {
			hash = 'cstool';
		}
	}
	var w = window.open('/calls/popup#' + hash, '_cstool', 'left=0,top=0,width=300,height=' + screen.height + ',menubar=no,toolbar=no,location=no,dialog=yes');
	w.moveTo(0, 0);
	w.resizeTo(300, screen.height);
}

function cs_search_popup(hash) {
	if ( typeof hash == 'undefined') {
		hash = 'cssearch';
	}
	var w = window.open('/users#' + hash, '_cssearch', 'left=301,top=0,width=' + (screen.width - 300) + ',height=' + screen.height + 'menubar=yes,toolbar=yes,location=yes,dialog=no');
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
	$(document).ajaxStart(function(){
		$('.ajaxLoadingIndicator').show('slide',{direction:'down'},250);
	});
	$(document).ajaxComplete(function(){
		$('.ajaxLoadingIndicator').stop(true, true);
		$('.ajaxLoadingIndicator').hide('slide',{direction:'down'},250);
	});
});
