<?php
/**
 * User: oefada
 * Date: 4/9/13
 * Time: 6:11 PM
 */

class HttpRequestComponent extends Object
{

    public function initialize(&$controller, $settings = array())
    {
        // saving the controller reference for later use
        $this->controller =& $controller;
    }
    /*
    *
    * Returns true if destination URL is reachable
    * otherwise it returns false.
    *
    * $url is a string for the url including protocol e.g. http(s)
    */
    public function check_response($url, $timeout = 10)
    {
        if (
            empty($url)
        ) {
            return false;
        }

        $ch = curl_init($url);

        // Set request options
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_NOBODY => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_USERAGENT => "page-check/1.0"
            )
        );
        // Execute request
        curl_exec($ch);
        // Check if an error occurred
        if (curl_errno($ch)) {
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
    public function isValidUrl($url)
    {

        if (empty($url)) {
            return false;
        }
        if (preg_match(
            '/^(http|https):\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_a-z]{2,5}' . '((:[0-9]{1,5})?\/.*)?$/i',$url)
        ) {
            return true;
        } else {
            return false;
        }
        //this way requires PHP > PHP 5.2
        //return filter_var($url, FILTER_VALIDATE_URL);//if it's valid it return TRUE else FALSE
    }
}