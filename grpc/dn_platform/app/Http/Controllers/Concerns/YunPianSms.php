<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Yunpian\Sdk\YunpianClient;
use function App\validateChinaPhoneNumber;
use App\VerificationCode;
use Illuminate\Support\Carbon;
use BadMethodCallException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

trait YunPianSms
{
    /**
     * verification code
     *
     * @var int
     */
    protected $code;

    /**
     * sms mobile
     *
     * @var string
     */
    protected $mobile;

    /**
     * sms template
     *
     * @var string
     */
    protected $template;

    /**
     * YunPian Sms
     *
     * @param string|null $mobile
     * @param string $tpl
     * @return array
     * @author lizx
     * @throws \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException
     */
    public function smsSend(?string $mobile, string $tpl = 'code'): array
    {
        if (! ($this->mobile = $mobile) || ! validateChinaPhoneNumber($mobile)) {
            throw new UnprocessableEntityHttpException('不是标准的手机号！');
        }

        if (! ($this->template = config('yunpian.template_' . $tpl, null)) || ! is_string($this->template)) {
            throw new UnprocessableEntityHttpException('对应模板不存在！请检查');
        }

        if (str_contains($tpl, '.')) {
            $tpl = str_before($tpl, '.');
        }

        $param = [
            YunpianClient::MOBILE => $mobile,
            YunpianClient::TEXT => call_user_func([$this, 'handle' . ucwords($tpl)])
        ];

        if (config('yunpian.mock_send')) {
            return call_user_func([$this, 'result'. ucwords($tpl)], $tpl);
        }

        // 初始化client,apikey作为所有请求的默认值
        $clnt = YunpianClient::create(config('yunpian.apikey'));
        $res = $clnt->sms()->single_send($param);

        // 账户$clnt->user() 签名$clnt->sign() 模版$clnt->tpl() 短信$clnt->sms() 语音$clnt->voice() 流量$clnt->flow() 视频短信$clnt->vsms()
        if ($res->isSucc()) {
            return call_user_func([$this, 'result' . ucwords($tpl)], $tpl);
        }

        return [$res];
    }

    /**
     * YunPian Verification code
     *
     * @param int $id
     * @param int $code
     * @return bool
     * @author lizx
     */
    public function verificateCode(int $id, int $code): bool
    {
        if (
            $verification = VerificationCode::find($id) &&
            $verification->code === $code &&
            Carbon::now()->lt(
                $verification->end_time
            )) {
            return $verification->update(['state' => VerificationCode::STATE_OFF]) ? true : false;
        };

        return false;
    }

    /**
     * tpl verification code
     *
     * @return string
     * @author lizx
     */
    private function handleCode(): string
    {
        $this->code = random_int(config('yunpian.code_min'), config('yunpian.code_max'));

        return sprintf($this->template, $this->code);
    }

    /**
     * verification code sms
     *
     * @param string $tpl
     * @return array
     * @author lizx
     */
    private function resultCode(string $tpl): array
    {
        if ($tpl === 'code') {
            $verification = new VerificationCode;
            $verification->user_id = request()->user()->id ?? null;
            $verification->mobile = $this->mobile;
            $verification->code = $this->code;
            $verification->end_time = Carbon::now()->addSeconds(config('yunpian.code_ttl'));

            if ($verification->save()) {
                if (config('yunpian.mock_send')) {
                    return ['verification_code_id' => $verification->id, 'code' => $this->code];
                }

                return ['verification_code_id' => $verification->id];
            }
        }

        return [];
    }

    /**
     * no handle[tpl]
     *
     * @param string $method
     * @param string|array $arguments
     * @return string|boolean
     * @author lizx
     * @throws \BadMethodCallException
     */
    public function __call($method, $arguments)
    {
        if (starts_with($method, 'handle')) {
            return $this->template;
        }

        if (starts_with($method, 'result')) {
            return ['result' => 'Success.']; // todo 默认返回写什么好？
        }

        throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
    }
}
