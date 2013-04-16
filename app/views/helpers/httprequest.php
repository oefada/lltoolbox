<?php
/**HTTP Request Helper
 * Created by JetBrains PhpStorm.
 * User: oefada
 * Date: 4/9/13
 * Time: 6:11 PM
 * To change this template use File | Settings | File Templates.
 *
 *  * HttpRequestHelper
 * This Helper provides a few functions for HttpRequests
 *
 * @author Onjefu Efada
 */

class HttprequestHelper extends AppHelper {
    /*
     *
     * Returns true if destination URL is reachable
     * otherwise it returns false.
     *
     * $url is a string for the url including protocol e.g. http(s)
     */

    public function check_response($url, $timeout = 10) {
        if (
            empty($url) ||
            !$this->isValidUrl($url)
        ){
            return FALSE;
        }

        $ch = curl_init($url);

        // Set request options
        curl_setopt_array($ch, array(
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_NOBODY => true,
          CURLOPT_TIMEOUT => $timeout,
          CURLOPT_USERAGENT => "page-check/1.0"
        ));

      // Execute request
        curl_exec($ch);

      // Check if an error occurred
        if(curl_errno($ch)) {
          curl_close($ch);
          return false;
        }

    // Get HTTP response code
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

    // return response_code
         return $code;
    }

    /**
     * will return true or false.
     *
     * @url - is string url
     */
    public function isValidUrl($url) {

        if(empty($url)){

            return FALSE;

        }else{
        return filter_var($url, FILTER_VALIDATE_URL);//if it's valid it return TRUE else FALSE

        }
    }

}

?>