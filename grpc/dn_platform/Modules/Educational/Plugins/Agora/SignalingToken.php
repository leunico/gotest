<?php

namespace Modules\Educational\Plugins\Agora;

class SignalingToken
{
    public static function getToken($appid, $appcertificate, $account, $validTimeInSeconds)
    {
        $SDK_VERSION = "1";

        $expiredTime = time() + $validTimeInSeconds;
        $token_items = array();
        array_push($token_items, $SDK_VERSION);
        array_push($token_items, $appid);
        array_push($token_items, $expiredTime);
        array_push($token_items, md5($account.$appid.$appcertificate.$expiredTime));

        return join(":", $token_items);
    }
}
