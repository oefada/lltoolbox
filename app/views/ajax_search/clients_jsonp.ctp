<?php header('Content-type: text/javascript');?>
if (typeof jsonp_search_handler == 'function') {
	jsonp_search_handler(<?php echo json_encode($jsonp);?>);	
}
