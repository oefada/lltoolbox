<?php
class HttpRequestComponent extends Object
{
    /**
     * @param $controller
     * @param array $settings
     */
    public function initialize(&$controller, $settings = array())
    {
        // saving the controller reference for later use
        $this->controller =& $controller;
    }

    /**
     *
     * Returns true if destination URL is reachable
     * otherwise it returns false.
     *
     * $url is a string for the url including protocol e.g. http(s)
     */
    public function check_response($url, $timeout = 10)
    {
        if (empty($url)) {
            return false;
        }

        $ch = curl_init($url);

        curl_setopt_array(
            $ch,
            array(
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_NOBODY => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_USERAGENT => "page-check/1.0",
                CURLOPT_SSL_VERIFYPEER => false
            )
        );

        curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

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
            '/^(http|https):\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_a-z]{2,5}' . '((:[0-9]{1,5})?\/.*)?$/i',
            $url
        )
        ) {
            return true;
        } else {
            return false;
        }
    }
}
