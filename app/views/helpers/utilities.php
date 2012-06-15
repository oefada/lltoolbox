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

	function clickSort($view, $field, $fieldTitle=null, $qArr=null){

		$url=$view->params['url']['url'];

		if (!strstr($url, "page:"))$url.="/page:1";
		if ($qArr){
			$keyArr=array();
			foreach($qArr as $key=>$val){
				if ($key=="url")continue;
				if (in_array($key,$keyArr)){
					continue;
				}
				$keyArr[]=$key;
				$url.="/".urlencode($key).":".urlencode($val);
			}
		}

		if (isset($view->params['named']['direction'])){
			if ($view->params['named']['direction']=='asc'){
				$url=str_replace(':asc',':desc',$url);
			}else{
				$url=str_replace(':desc',':asc',$url);
			}
		}else{
			$url.="/direction:desc";
		}
		if (isset($view->params['named']['sort'])){
			$url=preg_replace("~/sort:".$view->params['named']['sort']."~is","/sort:$field",$url);
		}else{
			$url.="/sort:$field";
		}
		
		$name=($fieldTitle!==null)?$fieldTitle:$field;
		return "<a href='/$url'>$name</a>";

	}

}

?>
