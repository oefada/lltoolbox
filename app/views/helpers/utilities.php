<?

class UtilitiesHelper extends AppHelper {

	function sortLink($field, $title, $view, $html,$url = "/reports/aging/sortBy:"){

		$url.=$field;

		if (isset($view->params['named']['sortBy']) && $view->params['named']['sortBy'] == $field) {
			$dir = ($view->params['named']['sortDirection'] == 'ASC') ? 'DESC' : 'ASC';
		} elseif(isset($view->params['named']['sortBy'])  && $view->params['named']['sortBy'] == $field) {
			$dir = 'DESC';
		} else {
			$dir = 'ASC';
		}
		
		$url .= "/sortDirection:$dir";
		
		return $html->link($title, $url);

	}


}

?>
