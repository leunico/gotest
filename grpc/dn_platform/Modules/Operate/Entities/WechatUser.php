<?php

namespace Modules\Operate\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;

class WechatUser extends Model
{
    protected $fillable = ['unionid','music_openid', 'art_openid'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createFromSession($category)
    {
        $wechatUser = $user = null;
        if ($category == 'music') {
            $wechatUser = session('wechat.oauth_user.music');
        } elseif ($category == 'art') {
            $wechatUser = session('wechat.oauth_user.art');
        }

        if (!empty($wechatUser)) {
            $original = $wechatUser->getOriginal();
            if (!empty($original['unionid'])) {
                $user = WechatUser::firstOrNew(['unionid' => $original['unionid']]);
            } else {
                $user = WechatUser::firstOrNew([$category . '_openid' => $wechatUser->getId()]);
            }
            $user->nickname = $wechatUser->getNickname();
            $user->sex = $original['sex'] ?? 0;
            $user->language = $original['language'] ?? null;
            $user->city = $original['city'] ?? null;
            $user->province = $original['province'] ?? null;
            $user->country = $original['country'] ?? null;
            $user->headimgurl = $wechatUser->getAvatar();

            $user->save();
        }

        return $user;
    }

    public static function getUnionidFromSession($category)
    {
        $unionid = $wechatUser = null;

        if ($category == 'music') {
            $wechatUser = session('wechat.oauth_user.music');
        } elseif ($category == 'art') {
            $wechatUser = session('wechat.oauth_user.art');
        }

        if (!empty($wechatUser)) {
            $original = $wechatUser->getOriginal();
            $unionid = !empty($original['unionid']) ? $original['unionid'] : null;
        }

        return $unionid;
    }
}
