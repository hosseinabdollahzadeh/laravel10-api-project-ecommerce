<?php

namespace Modules\Payment\Gateways\Zibal;

class Zibal
{
    public function send($merchant, $amount, $callbackUrl, $mobile, $description)
    {
        return $this->curl_post('https://gateway.zibal.ir/v1/request', [
            'merchant' => $merchant,
            'amount' => $amount,
            'callbackUrl' => $callbackUrl,
            'mobile' => $mobile,
            'description' => $description,
        ]);
    }

    function verify($merchant,$trackId)
    {
        return $this->curl_post('https://gateway.zibal.ir/v1/verify', [
            'merchant' => $merchant,
            'trackId' => $trackId,
        ]);
    }

    public function curl_post($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
}
