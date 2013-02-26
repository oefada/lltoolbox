<?php

class NileGuideApi extends Model
{

	private static $basePath = 'http://www.nileguide.com/service/';
	private static $apiKey = 'c24af97e-7a2e-46aa-95fc-9b3c5cde6c8f';
	private static $headers = array();

	public static function fetch($rest, $params = array(), $format = 'json')
	{
		switch($format) {
			case 'json' :
				self::$headers['Accept'] = 'application/json';
				break;
			case 'xml' :
				self::$headers['Accept'] = 'application/xml';
				break;
		}
		$params['key'] = self::$apiKey;
		$path = self::$basePath . $rest . '?';
		$prefix = '';
		foreach ($params as $k => $v) {
			$path .= $prefix . urlencode($k) . '=' . urlencode($v);
			$prefix = '&';
		}
		$cacheKey = sha1(serialize(array(
			'path' => $path,
			'params' => $params,
			'headers' => self::$headers,
		)));
		if (!is_dir('/tmp/nileguide/')) {
			mkdir('/tmp/nileguide/');
		}
		$cacheFile = '/tmp/nileguide/cache-' . $cacheKey . '.delme';
		echo "--== [ ";
		echo preg_replace('/key=[^&]+/', 'key=REDACTED', $path);
		echo " ] ==--\n";
		if (file_exists($cacheFile) && ((time() - filemtime($cacheFile)) < (60 * 60 * 24 * 7))) {
			echo "NileGuideApi: Cache hit.\n";
			$data = file_get_contents($cacheFile);
		} else {
			echo "NileGuideApi: Not cached, fetching from API...\n";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $path);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if (self::$headers) {
				$headerArray = array();
				foreach (self::$headers as $k => $v) {
					$headerArray[] = $k . ': ' . $v;
				}
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
			}
			$data = curl_exec($ch);
			curl_close($ch);
			file_put_contents($cacheFile, $data);
		}
		switch($format) {
			case 'json' :
				return json_decode($data, true);
				break;
			case 'xml' :
				return simplexml_load_string($data);
				break;
			default :
				return $data;
		}
	}

}
