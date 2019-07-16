<?php

namespace Modules\Examinee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Examination\Entities\ExaminationExaminee;
use Modules\ExaminationPaper\Entities\Question;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ExamineeAnswerMinecaftRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'scratch' => 'required|array',
            'from' => [
                'required',
                'json',
                function ($attribute, $value, $fail) {
                    $val = collect(json_decode($value, true));
                    if (empty($val->get('question_id')) || 
                        empty($val->get('examination_id')) || 
                        empty($val->get('answer')) || 
                        empty($val->get('answer_time'))) {
                        return $fail('错误：数据不齐全！');
                    }

                    $eexam = ExaminationExaminee::select('id', 'is_hand')
                        ->where('examination_id', $val->get('examination_id'))
                        ->where('examinee_id', $this->user()->id)
                        ->first();

                    if (empty($eexam)) {
                        return $fail('考生考试信息不存在，请不要瞎几把操作！');
                    }

                    if (! empty($eexam->is_hand)) {
                        return $fail('你已经交卷了，不可再答题！');
                    }

                    $question = Question::select('questions.id')
                        ->leftjoin('major_problems', 'questions.major_problem_id', 'major_problems.id')
                        ->where('questions.id', $val->get('question_id'))
                        ->where('examination_id', $val->get('examination_id'))
                        ->first();
                            
                    if (empty($question)) {
                        return $fail('题目不存在或者不属于当前考试，请不要搞事情！');
                    }

                    $this->offsetSet('efrom', $val);
                }
            ]
        ];

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @param \Tymon\JWTAuth\JWTAuth $auth
     * @return bool
     */
    public function authorize(JWTAuth $auth)
    {
        $this->merge(['token' => json_decode($this->from, true)['token'] ?? null]);
        try {
            if (! $auth->parser()->setRequest($this)->hasToken()) {
                throw new UnauthorizedHttpException('jwt-auth', 'Token not provided');
            }
            
            if (! $auth->parseToken()->authenticate()) {
                throw new AuthenticationException('User not found');
            }
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
        } catch (TokenInvalidException $e) {
            throw new AuthenticationException;
        }

        return true;
    }
}
