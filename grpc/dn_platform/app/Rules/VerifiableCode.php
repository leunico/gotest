<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\VerificationCode;
use Carbon\Carbon;

class VerifiableCode implements Rule
{
    /**
     * mobile
     *
     * @var string
     */
    protected $mobile;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $verify = VerificationCode::where('mobile', $this->mobile)
            ->where('code', $value)
            ->where('state', VerificationCode::STATE_ON)
            ->orderby('id', 'desc')
            ->first();

        return isset($verify->end_time) && Carbon::now()->lt($verify->end_time) && $verify->update(['state' => VerificationCode::STATE_OFF]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '验证码不正确！';
    }
}
