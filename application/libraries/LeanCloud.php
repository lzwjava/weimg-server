<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 16/1/22
 * Time: 上午12:15
 */
class LeanCloud
{
    function curlLeanCloud($path, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.leancloud.cn/1.1/" . $path);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "X-LC-Id: SLC5re13KRwB0sQA3lJnaoFD-gzGzoHsz",
            "X-LC-Key: yDyoebpPe8dHjbc9txPVLKAD",
            "Content-Type: application/json"
        ));
        if ($data == null) {
            $data = new stdClass();
        }
        $encoded = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status < 200 || $status >= 300) {
            $resultJson = json_decode($result);
            if ($resultJson && isset($resultJson->error)) {
                $result = $resultJson->error;
            }
        }
        if ($result === false) {
            $result = 'Network error when send smsCode';
        }
        return array(
            "status" => $status,
            "result" => $result
        );
    }

    function sendTemplateSms($phone, $template, $data)
    {
        $data[SMS_TEMPLATE] = $template;
        $data[KEY_MOBILE_PHONE_NUMBER] = $phone;
        if (ENVIRONMENT != 'development') {
            $result = $this->curlLeanCloud("requestSmsCode", $data);
            if ($result["status"] != 200) {
                $string = json_encode($result["result"]);
                logInfo("requestSmsCode error result: $string");
            } else {
                logInfo("send sms code succeed. data: " . json_encode($data));
            }
        } else {
            logInfo("imitate requestSmsCode data: " . json_encode($data));
        }
    }
}
