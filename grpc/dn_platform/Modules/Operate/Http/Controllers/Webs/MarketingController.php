<?php

namespace Modules\Operate\Http\Controllers\Webs;

use function App\formatValidationErrors;
use function App\getChannel;
use function App\responseFailed;
use App\User;
use App\VerificationCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Operate\Entities\Channel;
use Modules\Operate\Entities\Lead;
use Modules\Operate\Entities\WechatUser;
use Zhuzhichao\IpLocationZh\Ip;

class MarketingController extends Controller
{
    public function centralMusic()
    {
        return view('operate::marketing.201901.central_sound');
    }

    public function centralMusic2()
    {
        return view('operate::marketing.201901.central_sound2');
    }

    public function music1V1()
    {
        $gradeMap = User::$gradeMap;
        unset($gradeMap[0]);
        $affair = \request('affair', 1);

        return view('operate::marketing.201901.music1v1', compact('gradeMap', 'affair'));
    }

    public function operationalAffair(Request $request)
    {
        $affair = $request->affair;
        return view('operate::marketing.201901.music1v1_success', compact('affair'));
    }

    public function save(Request $request)
    {
        $rules = [
            'age' => 'integer|min:5|max:16',
            'sex' => 'integer|in:1,2',
            'mobile' => 'required|cn_phone',
            'name' => 'required|username|display_length:2,6',
            'verify_code' => 'required',
            'verify_code_id' => 'required',
            'grade' => 'required_if:need_grade,1'
        ];

        $validator = \Validator::make($request->all(), $rules, [
            'name.username' => '姓名格式不正确',
            'name.display_length' => '姓名长度必须在2-6之间'
        ], [
            'age' => '年龄',
            'sex' => '性别',
            'mobile' => '手机号',
            'name' => '姓名',
            'verify_code' => '验证码',
            'verify_code_id' => '验证码id',
            'grade' => '年级',
            'need_grade' => '需要填写年级'
        ]);

        if ($validator->fails()) {
            return $this->response()->errorMsg(formatValidationErrors($validator));
        }

        //检查验证码
        if (!$this->verificateCode($request->verify_code_id, $request->verify_code)) {
            return $this->response()->errorMsg('验证码不正确');
        }
        //创建用户
        try {
            DB::beginTransaction();

            $user = User::where('phone', $request->mobile)->first();
            $isNew = 0;
            if (empty($user)) {
                $user = User::marketingCreate($request);
                $isNew = 1;
            }

            //插入线索
            if (empty($request->tag)) {
                $lead = Lead::where('mobile', $request->mobile)->first();
            } else {
                $lead = Lead::where('mobile', $request->mobile)->where('tag', $request->tag)->first();
            }

            if (empty($lead)) {
                $ipInfo = Ip::find($request->ip());

                //todo channel_id
                $lead = new Lead();
                $lead->user_id = $user->id;
                $lead->name = $request->name ?? null;
                $lead->mobile = $request->mobile;
                $lead->ip = $request->ip();
                $lead->ip_region = implode(' ', $ipInfo); //todo
                $lead->grade = $request->grade ?? 0;
                $lead->sex = $request->sex ?? 0;
                $lead->age = $request->age ?? 0;
                $lead->is_new = $isNew;
                $lead->device = \Agent::platform();
                $lead->user_agent = \Agent::getUserAgent();

                $lead->tag = $request->tag;
                $lead->operational_affair = $request->operational_affair ?? 0;
                $lead->channel_id = getChannel();
                $wechatUser = WechatUser::createFromSession($request->oauth_category);
                if (!empty($wechatUser)) {
                    $lead->unionid = $wechatUser->unionid;
                }
                $lead->referer = $request->header('referer') ?? null;
                $lead->save();
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->response()->errorMsg($exception->getMessage());
        }

        return $this->response()->success(['lead_id' => $lead->id]);
    }


    /**
     * YunPian Verification code
     *
     * @param int $id
     * @param int $code
     * @return bool
     * @author lizx
     */
    protected function verificateCode(int $id, int $code): bool
    {
        if ( $verification = VerificationCode::find($id)) {
            if ($verification->code == $code &&
                Carbon::now()->lt($verification->end_time)) {
                return $verification->update(['state' => VerificationCode::STATE_OFF]) ? true : false;
            }
        }
        return false;
    }

    /**
     * 活动统计并跳转
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleActivity()
    {
        $slug = request('slug');
        $utm_medium = \request('utm_medium');
        $utm_term = \request('utm_term');
        $utm_content = \request('utm_content');

        if ($slug) {
            $channel = Channel::whereSlug($slug)->where('level', 4)->firstOrFail();
            $channel->view_count++;
            $channel->save();

            session()->put('from_activity_id', $channel->id);

            session()->put('utm_medium', $utm_medium);
            session()->put('utm_term', $utm_term);
            session()->put('utm_content', $utm_content);

            $redirect_to = request('redirect_to');
            if ($redirect_to) {
                return redirect($redirect_to);
            } else {
                $growingio_params = [
                    'utm_source' => $channel->title,
                ];

                $parse_url = parse_url($channel->link);
                $query_params = [];
                if (!empty($parse_url['query'])) {
                    parse_str($parse_url['query'], $query_params);
                }
                if (empty($parse_url['path'])) {
                    $parse_url['path'] = '';
                }

                if (!empty($utm_medium)) {
                    $query_params['utm_medium'] = $utm_medium;
                }

                if (!empty($utm_term)) {
                    $query_params['utm_term'] = $utm_term;
                }

                if (!empty($utm_content)) {
                    $query_params['utm_content'] = $utm_content;
                }

                if (!empty($parse_url['scheme']) and !empty($parse_url['host'])) {
                    $final_link = $parse_url['scheme'] . '://' . $parse_url['host'] . $parse_url['path'] . '?' . http_build_query(array_merge($query_params, $growingio_params));
                } else {
                    $final_link = $channel->link;
                }

                return redirect($final_link);
            }
        } else {
            abort(404);
        }
    }

    public function artCode()
    {
        return view('operate::marketing.201902.art_code');
    }

    public function artCodeMobile()
    {
        return view('operate::marketing.201902.art_code_mobile');
    }

}
