<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 5/13/16
 * Time: 2:42 PM
 */
class Crawler extends BaseController
{
    public $downloader = NULL;

    function __construct()
    {
        parent::__construct();
        $this->load->library(Downloader::class);
        $cookie = 'SINAGLOBAL=1940303235314.7864.1409335847229; __utma=182865017.1416456124.1438877164.1438877164.1438877164.1; tma=15428400.78445988.1440004350526.1440004350526.1440004350526.1; bfd_g=83bd782bcb74fca200003b52000234c950ab4415; Hm_lvt_f6073ff592b43bb4250e644e2872692d=1440348986,1440350013,1440353620,1440354688; _ga=GA1.2.1416456124.1438877164; __gads=ID=ed84888e3dc126f6:T=1458173691:S=ALNI_MbnXk_kWjdm3kMp0gqGJ9Eobhr9IQ; YF-Ugrow-G0=169004153682ef91866609488943c77f; YF-V5-G0=a9b587b1791ab233f24db4e09dad383c; _s_tentry=login.sina.com.cn; Apache=1367074155057.1462.1462104737189; ULV=1462104737214:99:1:1:1367074155057.1462.1462104737189:1459605431491; YF-Page-G0=ed0857c4c190a2e149fc966e43aaf725; wb_g_minivideo_1695406573=1; TC-Ugrow-G0=968b70b7bcdc28ac97c8130dd353b55e; login_sid_t=f00d89e27f771863ff4eac2aef198678; appkey=; myuid=1695406573; WBtopGlobal_register_version=60539f809b40ed0d; un=651142978@qq.com; WBStore=8ca40a3ef06ad7b2|undefined; wvr=6; TC-V5-G0=8518b479055542524f4cf5907e498469; SSOLoginState=1463079578; UOR=www.csdn.net,widget.weibo.com,login.sina.com.cn; TC-Page-G0=fd45e036f9ddd1e4f41a892898506007; SUS=SID-1695406573-1463124678-XD-vsczm-a6ba8e8ddb18454c6ffa573db15625b7; SUE=es%3D375c22bdfe6a0f60a71d0c1d50c853e8%26ev%3Dv1%26es2%3D22fdb9e6408b44e514675cfadb20123d%26rs0%3Dw8PjKUiUXY%252BPfSFjHH%252FW%252BMn7%252Bg6YQQTxwk64d7dpLDaqFxLGiizMxI3cXEVv0%252BMzD1JuBTsB3XG7ZA%252FjRNgCxBZh7TFAVHJlIu2L1QToOqxcmrn42w3VSjd7GTEUCGalfr9L4XWTcuuQy2zdgeBpOF%252FKXlxvxMrhcQRStRN1%252BJQ%253D%26rv%3D0; SUP=cv%3D1%26bt%3D1463124678%26et%3D1463211078%26d%3Dc909%26i%3D25b7%26us%3D1%26vf%3D0%26vt%3D0%26ac%3D2%26st%3D0%26uid%3D1695406573%26name%3D651142978%2540qq.com%26nick%3DAccepted%26fmp%3D%26lcp%3D2015-08-16%252001%253A38%253A50; SUB=_2A256MfKWDeRxGedI4lcV8CjJzD-IHXVZR2NerDV8PUNbu9APLXbEkW9LHetSGXHUsiAkNKxND5sluTTecp2J6w..; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9WWTYL798UYl1FKG5fYD-Fic5JpX5KzhUgL.Fo2c1K-XehqfS0et; SUHB=0KAwsLKxVdcveK; ALF=1494660677';
        $this->downloader = new Downloader($cookie);
        $this->downloader->proxy = '127.0.0.1:8888';
    }

    private function processHtml($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $scripts = $dom->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $scriptStr = $script->ownerDocument->saveXML($script);
            if (preg_match('/feed_list_content/', $scriptStr)) {
                $matches = array();
                $match = preg_match('/"html":"(.+)"/', $scriptStr, $matches);
                if ($match) {
                    $target = $matches[1];
                    $replace = preg_replace('/\\\\([\\/\\"])/', '$1', $target);
                    $replace = preg_replace('/\\\\n/', '', $replace);
                    return $replace;
                } else {
                    logInfo("not match");
                }
            }
        }
        return null;
    }

    private function removeQuotes($value)
    {
        return substr($value, 1, strlen($value) - 2);
    }

    private function printContent($html)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query("//div[@node-type]") as $rowNode) {
            $value = $rowNode->getAttribute('node-type');
            //$value = $this->removeQuotes($value);
            //logInfo($value);
            if ($value == 'feed_list_content') {
                //logInfo("found");
                $str = $rowNode->ownerDocument->saveXML($rowNode);
                logInfo("str: " . $str);
            }
        }
    }

    function crawl_get()
    {
        $resp = $this->downloader->download('http://weibo.com/1771123430/Dveoyc6Ng');
        $replace = $this->processHtml($resp);
        logInfo($replace);
        $this->printContent($replace);
        //logInfo($replace);
        $this->succeed();
    }
}
