<?
// load Smarty library
require('Smarty.class.php');

$main_path = $_SERVER['DOCUMENT_ROOT'];

class Smarty_LuxuryLink_Admin extends Smarty {
   // Class Constructor.
   // Variables set with each new instance.
   function Smarty_LuxuryLink_Admin() {
   		global $main_path;
		$this->Smarty();
	
		// Create hard paths to the Smarty directories
		$this->compile_dir = $main_path . '/tmp/';
		$this->cache_dir = $main_path . '/tmp/cache/';
		
		$this->register_function('remove_html_tags', 'remove_html_tags');
			
		if (!@$_GET['debug']) {
			$this->force_compile = false; 	
			$this->caching = true;			
			$this->cache_lifetime = 3600*24;  // Set a one day cache
			$this->compile_check = false;	
		} else {
			$this->force_compile = true; 	// remove this line after testing complete)
			$this->caching = false;			// Turn on to cache the application
			$this->compile_check = true;	// To be set false on the live site
		}

	}
}

/*
	function assign_all_vars
	Assigns all variables in the global space as smarty variables
*/
function assign_all_vars(&$smarty){
	foreach ($GLOBALS as $key=>$value){
		$smarty->assign($key, $value);
	}
}
function remove_html_tags($params) {
	$pattern = "/<\/*\w+>/";
	$text = preg_replace($pattern, '', $params['text']);
	return $text;
}
?>
