<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 16/2/6
 * Time: 上午2:12
 */

use Qiniu\Auth;

class Files extends BaseController
{
    private function getUpToken()
    {
        $bucket = 'weimg';
        $accessKey = '-ON85H3cEMUaCuj8UFpLELeEunEAqslrqYqLbn9g';
        $secretKey = 'X-oHOYDinDEhNk5nr74O1rKDvkmPq0ZQwEZfFt6x';
        $auth = new Auth($accessKey, $secretKey);
        $upToken = $auth->uploadToken($bucket);
        return $upToken;
    }

    public function uptoken_get()
    {
        $upToken = $this->getUpToken();
        $bucketUrl = "http://7xqmlm.com1.z0.glb.clouddn.com";
        $result = array(
            "key" => getToken(6),
            "uptoken" => $upToken,
            "bucketUrl" => $bucketUrl
        );
        $this->succeed($result);
    }
}
