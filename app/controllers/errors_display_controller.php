<?

class ErrorsDisplayController extends AppController{


	function index(){

		$file='/home/html/toolbox/app/tmp/logs/error.log';
		$str=file_get_contents($file);
		echo $str."|";

	}


}
