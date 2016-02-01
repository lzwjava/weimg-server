<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 16/1/29
 * Time: 下午5:06
 */

use Qiniu\Auth;

class Images extends BaseController
{
    public $imageDao;

    function __construct()
    {
        parent::__construct();
        $this->load->model('imageDao');
        $this->load->helper('url');
        $this->imageDao = new ImageDao();
    }

    private function urlIsWorking($url = FALSE)
    {
        if (!$url) {
            return FALSE;
        }
        $url = prep_url($url);
        $header_arr = @get_headers($url);
        if (!$header_arr) {
            return false;
        } else {
            return in_array('HTTP/1.0 200 OK', $header_arr) ||
            in_array('HTTP/1.1 200 OK', $header_arr);
        }
    }

    private function checkIfTokenInvalid($token, $atLeastLen = 6)
    {
        $chars = tokenChars();
        $len = strlen($token);
        for ($i = 0; $i < $len; $i++) {
            $str = substr($token, $i, 1);
            if (!strpos($chars, $str)) {
                $this->failure(ERROR_PARAMETER_ILLEGAL, 'id 中包含了非法字符');
                return true;
            }
        }
        if ($len < $atLeastLen) {
            $this->failure(ERROR_PARAMETER_ILLEGAL, 'id 过短');
            return true;
        }
        return false;
    }

    function create_post()
    {
        if ($this->checkIfParamsNotExist($this->post(), array(KEY_LINK, KEY_IMAGE_ID))) {
            return;
        }
        $imageId = $this->post(KEY_IMAGE_ID);
        $link = $this->post(KEY_LINK);
        $desc = $this->post(KEY_DESCRIPTION);
        $user = $this->checkAndGetSessionUser();
        if (!$user) {
            return;
        }
        if ($this->checkIfTokenInvalid($imageId)) {
            return;
        }
        $this->imageDao->addImage($imageId, $link, $user->userId, $desc);
        $this->succeed();
    }

    function fetch_get($imageId)
    {
        $this->succeed($this->imageDao->getImage($imageId));
    }

    private function getUpToken()
    {
        $bucket = 'weimg';
        $accessKey = '-ON85H3cEMUaCuj8UFpLELeEunEAqslrqYqLbn9g';
        $secretKey = 'X-oHOYDinDEhNk5nr74O1rKDvkmPq0ZQwEZfFt6x';
        $auth = new Auth($accessKey, $secretKey);
        $upToken = $auth->uploadToken($bucket);
        return $upToken;
    }

    public function upload_get()
    {
        $upToken = $this->getUpToken();
        $bucketUrl = "http://7xqmlm.com1.z0.glb.clouddn.com";
        $result = array(
            "imageId" => getToken(7),
            "uptoken" => $upToken,
            "bucketUrl" => $bucketUrl
        );
        $this->succeed($result);
    }
}
