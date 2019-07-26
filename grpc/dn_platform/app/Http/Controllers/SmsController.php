<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Operate\Entities\Lead;
use App\Http\Controllers\Concerns\YunPianSms;

class SmsController extends Controller
{
    use YunPianSms;

    /**
     * Send sms [云片短信]
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function send(Request $request)
    {
        // if (empty($request->mobile)) {
        //     return $this->response()->errorUnprocessableEntity('手机号不能为空');
        // }

        // todo 如果是线索，判断有没有对应的leads记录 Ps.特殊短信验证码请求其实应该另外写一个接口处理！
        if (! empty($request->leads) && $request->mobile) {
            if (! empty(Lead::where('mobile', $request->mobile)->first())) {
                return $this->response()->errorMsg('您已领取该课程礼包，无需重复获取');
            }
        }

        if (! empty($request->tag)) {
            if (!empty(Lead::where('tag', $request->tag)->where('mobile', $request->mobile)->first())) {
                return $this->response()->errorMsg('您已领取，无需重复提交');
            }
        }

        return $this->response()->success($this->smsSend($request->mobile, $request->input('tpl', 'code.art')));
    }
}
