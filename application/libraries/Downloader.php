<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 5/13/16
 * Time: 2:44 PM
 */
class Downloader
{
    protected $headers = NULL;
    public $proxy = NULL;

    function __construct($cookie = NULL)
    {
        $this->headers = array(
            'User-Agent: Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) ' .
            'Chrome/29.0.1547.65 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.8,zh-CN;q=0.6,zh;q=0.4',
            'Accept-Encoding: gzip,deflate,sdch',
            'Connection: keep-alive',
        );
        if ($cookie) {
            array_push($this->headers, 'Cookie: ' . $cookie);
        }
    }

    function download($url, $params = '')
    {
        if ($params != '') {
            $encoded = '?' . urlencode($params);
            $url .= $encoded;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // logInfo("headers " . json_encode($this->headers));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        if ($this->proxy != NULL) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

        $resp = curl_exec($ch);
        curl_close($ch);
        //logInfo($resp);
        return $resp;
    }
}
