<?php
if (!isset($_SERVER['ENV'])) $_SERVER['ENV'] = '';
if (!isset($_SERVER['ENV_USER'])) $_SERVER['ENV_USER'] = '';

$debug = 0;
if (isset($_SERVER['ENV']) && $_SERVER['ENV'] == 'development') $debug = 2;
Configure::write('debug', $debug);
Configure::write('App.encoding', 'ISO-8859-15');
//Configure::write('Cache.disable', true);
Configure::write('Cache.check', true);
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

//Cache::config('default', array('engine' => 'File'));
$webservice_live_url = 'http://toolbox.luxurylink.com';
$ll_url = 'www.luxurylink.com';
$fg_url = 'www.familygetaway.com';

if (stristr($_SERVER['HTTP_HOST'], 'dev') || $_SERVER['ENV'] == 'development' || strpos($_ENV['HOSTNAME'], 'dev') !== FALSE) {
    define("ISDEV", true);
    define("ISSTAGE", false);
    $ll_url = 'dev-luxurylink.luxurylink.com';
    $fg_url = 'dev-familygetaway.luxurylink.com';
    $webservice_live_url = 'http://dev-toolbox.luxurylink.com';
    Configure::write('TokenizerService', 'tokenex_v2');
    Configure::write('TokenEx.tokenExV2Url', 'https://test-api.tokenex.com:8081/TokenServices.svc/REST/');
    Configure::write('TokenEx.tokenExV2ID', '4700943473181519');
    Configure::write('TokenEx.tokenExV2APIKey', 'NulLHqEpmVfJCF6t3wQJ');
    Configure::write('LltgApiUrl', 'dev.api.luxurylink.com');
} elseif (stristr($_SERVER['HTTP_HOST'], 'stage') || $_SERVER['ENV'] == 'staging' || strpos($_ENV['HOSTNAME'], 'stage') !== FALSE) {
    // TODO: Remove this block once we've migrated off of old stage
    define("ISDEV", true);
    define("ISSTAGE", true);
    $ll_url = 'stage-luxurylink.luxurylink.com';
    $fg_url = 'stage-family.luxurylink.com';
    $webservice_live_url = 'http://stage-toolbox.luxurylink.com';
    Configure::write('TokenizerService', 'tokenex_v2');
    Configure::write('TokenEx.tokenExV2Url', 'https://test-api.tokenex.com:8081/TokenServices.svc/REST/');
    Configure::write('TokenEx.tokenExV2ID', '4700943473181519');
    Configure::write('TokenEx.tokenExV2APIKey', 'NulLHqEpmVfJCF6t3wQJ');
} elseif (stristr($_SERVER['HTTP_HOST'], 'uat-toolbox')) {
    define("ISDEV", true);
    define("ISSTAGE", true);
    $ll_url = 'uat-luxurylink.luxurylink.com';
    $fg_url = 'uat-family.luxurylink.com';
    $webservice_live_url = 'http://uat-toolbox.luxurylink.com';
    Configure::write('TokenizerService', 'tokenex_v2');
    Configure::write('TokenEx.tokenExV2Url', 'https://test-api.tokenex.com:8081/TokenServices.svc/REST/');
    Configure::write('TokenEx.tokenExV2ID', '4700943473181519');
    Configure::write('TokenEx.tokenExV2APIKey', 'NulLHqEpmVfJCF6t3wQJ');
    Configure::write('LltgApiUrl', 'uat.api.luxurylink.com');
} else {
    define("ISDEV", false);
    define("ISSTAGE", false);
    Configure::write('TokenizerService', 'tokenex_v2');
    Configure::write('TokenEx.tokenExV2Url', 'https://api.tokenex.com/TokenServices.svc/REST/');
    Configure::write('TokenEx.tokenExV2ID', '7671187692728770');
    Configure::write('TokenEx.tokenExV2APIKey', 'zcRrwhnTE7Y5RvYm9JiA');
}

if (ISDEV) {
    Cache::config('default', array(
        'engine' => 'Apc', //[required]
        'duration' => 3600, //[optional]
        'probability' => 100, //[optional]
        'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
    ));
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

// TICKET4334: The below is neccessary for PHP 5.x.x < 5.2.x
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

