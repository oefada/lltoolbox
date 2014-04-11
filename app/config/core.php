<?php
if (!isset($_SERVER['ENV'])) $_SERVER['ENV'] = '';
if (!isset($_SERVER['ENV_USER'])) $_SERVER['ENV_USER'] = '';

$debug = (isset($_SERVER['ENV']) && $_SERVER['ENV'] == 'development') ? 2 : 0;
Configure::write('debug', $debug);
Configure::write('App.encoding', 'ISO-8859-15');
Configure::write('Cache.check', true);
Cache::config('default', array('engine' => 'File'));
$cacheAction = true;

define('LOG_ERROR', 2);
Configure::write('Session.save', 'php');
Configure::write('Session.cookie', 'CAKEPHP');
Configure::write('Session.timeout', '7200');
Configure::write('Session.start', true);
Configure::write('Session.checkAgent', false);
Configure::write('Security.level', 'low');
Configure::write('Security.salt', '672fac72359e51f017c6355356d07d42137082d4');
Configure::write('Acl.classname', 'DbAcl');
Configure::write('Acl.database', 'default');

/**** Site Specific Configuration ****/
$webservice_live_url = 'http://toolbox.luxurylink.com';
$ll_url = 'www.luxurylink.com';
$fg_url = 'www.familygetaway.com';

if (stristr($_SERVER['HTTP_HOST'], 'dev') || $_SERVER['ENV'] == 'development' || strpos($_ENV['HOSTNAME'], 'dev') !== FALSE) {
    define("ISDEV", true);
    define("ISSTAGE", false);
    $ll_url = 'dev-luxurylink.luxurylink.com';
    $fg_url = 'dev-familygetaway.luxurylink.com';
    $webservice_live_url = 'http://dev-toolbox.luxurylink.com';
    Configure::write('Cache.disable', true);
    Configure::write('LltgApiUrl', 'dev.api.luxurylink.com');
    Configure::write('TokenizerService', 'api');
    Configure::write('TokenizerOptions.lltgAPIUrl', Configure::read('LltgApiUrl'));
} elseif (stristr($_SERVER['HTTP_HOST'], 'uat-toolbox')) {
    define("ISDEV", true);
    define("ISSTAGE", true);
    $ll_url = 'uat-luxurylink.luxurylink.com';
    $webservice_live_url = 'http://uat-toolbox.luxurylink.com';
    Configure::write('LltgApiUrl', 'uat.api.luxurylink.com');
    Configure::write('TokenizerService', 'api');
    Configure::write('TokenizerOptions.lltgAPIUrl', Configure::read('LltgApiUrl'));
} elseif (stristr($_SERVER['HTTP_HOST'], 'uat-internal-toolbox')) {
    define("ISDEV", true);
    define("ISSTAGE", true);
    $ll_url = 'uat-internal-luxurylink.luxurylink.com';
    $webservice_live_url = 'http://uat-internal-toolbox.luxurylink.com';
    Configure::write('LltgApiUrl', 'uat.internal.api.luxurylink.com');
    Configure::write('TokenizerService', 'api');
    Configure::write('TokenizerOptions.lltgAPIUrl', Configure::read('LltgApiUrl'));
} else {
    define("ISDEV", false);
    define("ISSTAGE", false);
    Configure::write('LltgApiUrl', 'api.luxurylink.com');
    Configure::write('TokenizerService', 'tokenex_v2');
    Configure::write('TokenizerOptions.tokenExV2Url', 'https://api.tokenex.com/TokenServices.svc/REST/');
    Configure::write('TokenizerOptions.tokenExV2ID', '7671187692728770');
    Configure::write('TokenizerOptions.tokenExV2APIKey', 'zcRrwhnTE7Y5RvYm9JiA');
}

$ll_urls = 'https://' . $ll_url;
$ll_url = 'http://' . $ll_url;
$fg_urls = 'https://' . $fg_url;
$fg_url = 'http://' . $fg_url;

Configure::write("OfferSite1", "offerLuxuryLink");
Configure::write("OfferSite2", "offerFamily");

Configure::write("Url.Ws", $webservice_live_url);
Configure::write("Url.LL", $ll_url);
Configure::write("Url.FG", $fg_url);
Configure::write("UrlS.LL", $ll_urls);
Configure::write("UrlS.FG", $fg_urls);

$abs_path = dirname(__file__);
$abs_path = str_replace('/config', '/', $abs_path);
define('APP_ABSOLUTE_PATH', $abs_path);

// The below is necessary for PHP 5.x.x < 5.2.x
if (!function_exists('array_fill_keys')) {
    function array_fill_keys($array, $values)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $arraydisplay[$array[$key]] = $values;
            }
        }
        return $arraydisplay;
    }
}

if (!function_exists('sys_get_temp_dir')) {
    function sys_get_temp_dir()
    {
        return '/tmp/';
    }
}

