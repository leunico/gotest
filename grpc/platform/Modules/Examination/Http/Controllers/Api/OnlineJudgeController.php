<?php

namespace Modules\Examination\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examination\Http\Controllers\ExaminationController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examination\Http\Requests\OnlineJudgeRequest;
use App\Models\Solution;
use Illuminate\Support\Carbon;
use App\Models\SourceCode;
use App\Models\CustomInput;
use Modules\Examination\Entities\ExaminationExaminee;
use App\Models\CompileInfo;
use App\Models\RuntimeInfo;

class OnlineJudgeController extends Controller
{
    /**
     * oj提交代码.
     *
     * @param \App\Http\Requests\OnlineJudgeRequest $request
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \App\Models\Solution $solution
     * @param \App\Models\SourceCode $sourceCode
     * @param \App\Models\CustomInput $customInput
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(
        OnlineJudgeRequest $request, 
        ExaminationExaminee $eexaminee, 
        Solution $solution, 
        SourceCode $sourceCode, 
        CustomInput $customInput): JsonResponse
    {
        $solution->problem_id = 0;
        $solution->user_id = $this->examinee()->id;
        $solution->examination_examinee_id = $eexaminee->id ?? 0;
        $solution->language = $request->language;
        $solution->result = Solution::RESULT_WAITING;
        $solution->in_date = Carbon::now();
        $solution->ip = $request->ip();
        $solution->code_length = strlen($request->source);

        $solution->getConnection()->transaction(function () use ($solution, $sourceCode, $request, $customInput) {
            if ($solution->save()) {
                $sourceCode->source = $request->source;
                $customInput->input_text = $request->input('input_text', '');
                $solution->sourceCode()->save($sourceCode);
                $solution->customInput()->save($customInput);
            }
        });

        return $this->response()->success(['solution_id' => $solution->solution_id]);
    }

    /**
     * oj获取运行结果.
     *
     * @param \App\Models\Solution $solution
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(Solution $solution): JsonResponse
    {
        $output = $solution->result == Solution::RESULT_COMPILE_ERR ? 
            CompileInfo::where('solution_id', $solution->solution_id)->first() : 
            RuntimeInfo::where('solution_id', $solution->solution_id)->first();

        return empty($output) ? 
            $this->response()->error() :
            $this->response()->success([
                'output' => $output->error,
                'solution' => $solution
            ]);
    }
}
