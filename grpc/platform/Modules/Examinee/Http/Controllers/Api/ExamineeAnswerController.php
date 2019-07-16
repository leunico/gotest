<?php

namespace Modules\Examinee\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examinee\Http\Requests\ExamineeAnswerRequest;
use Modules\Examinee\Entities\ExamineeAnswer;
use Modules\Examination\Entities\Examination;
use Modules\Examination\Entities\ExaminationExaminee;
use Illuminate\Support\Facades\DB;
use Modules\ExaminationPaper\Jobs\MarkingObjectiveJob;
use Illuminate\Support\Carbon;
use Modules\Examinee\Transformers\ExamineeAnswerResource;
use App\Traits\FileHandle;
use Modules\Examinee\Http\Requests\ExamineeAnswerMinecaftRequest;
use Modules\ExaminationPaper\Entities\Question;

class ExamineeAnswerController extends Controller
{
    use FileHandle;

    /**
     * 答题.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeAnswerRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(ExamineeAnswerRequest $request): JsonResponse
    {
        $answer = ExamineeAnswer::firstOrNew([
            'examinee_id' => $this->examinee()->id,
            'examination_id' => $request->examination_id,
            'question_id' => $request->question_id
        ]);

        $answer->question_option_id = $request->input('question_option_id', 0);
        $answer->answer = $request->answer;
        $answer->answer_file = $request->answer_file ? $this->resetAnswerFile($request->answer_file) : [];
        $answer->answer_time = $request->answer_time;
        $answer->type = $request->type;
        $answer->save();

        return $this->response()->item($answer, ExamineeAnswerResource::class);
    }

    /**
     * 答题（修改）.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeAnswerRequest $request
     * @param \Modules\Examinee\Entities\ExamineeAnswer $answer
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(ExamineeAnswerRequest $request, ExamineeAnswer $answer): JsonResponse
    {
        $answer->question_option_id = $request->input('question_option_id', 0);
        $answer->answer = $request->answer;
        $answer->answer_time = $request->answer_time;
        $answer->answer_file = $request->answer_file ? $this->resetAnswerFile($request->answer_file) : $answer->answer_file;

        return $this->response()->success($answer->save());
    }

    /**
     * Mc答题部分.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeAnswerMinecaftRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function mcStore(ExamineeAnswerMinecaftRequest $request): JsonResponse
    {
        $answer = ExamineeAnswer::firstOrNew([
            'examinee_id' => $this->examinee()->id,
            'examination_id' => $request->efrom->get('examination_id'),
            'question_id' => $request->efrom->get('question_id')
        ]);

        $answer->answer = $request->efrom->get('answer');
        $answer->answer_time = $request->efrom->get('answer_time');
        $answer->answer_file = $request->scratch[0] ? $this->resetAnswerFile($request->scratch[0], 'bluebridge/material/scratch/media') : [];
        $answer->type = Question::CATEGORY_OPERATION;
        $answer->save();

        return $this->response()->item($answer, ExamineeAnswerResource::class);
    }

    /**
     * 解答数据.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Examinee\Entities\ExamineeAnswer $answer
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function jsonProject(Request $request, ExamineeAnswer $answer): JsonResponse
    {
        // if ($request->routeIs('answer-project')) {
        //     return $this->response()->success($answer->answer_file);
        // }

        if (! empty($answer->answer_file) && isset($answer->answer_file['json_url'])) {
            $data = file_get_contents($answer->answer_file['json_url']);
        }

        if (empty($data)) {
            $data = file_get_contents(public_path('default_mc.json'));
        }

        return $this->response()->success(json_decode($data));
    }

    /**
     * 答题最新时间进度.
     *
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function time(Examination $examination): JsonResponse
    {
        $examination->load([
            'examineeAnswer' => function ($query) {
                $query->select('id', 'examination_id', 'examinee_id', 'answer_time')
                    ->where('examinee_id', $this->examinee()->id)
                    ->orderBy('answer_time', 'desc');
            }
        ]);

        $data['time'] = empty($examination->examineeAnswer) ? 0 : $examination->examineeAnswer->first()->answer_time;

        return $this->response()->success($data);
    }

    /**
     * 交卷.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function handIn(ExaminationExaminee $eexaminee): JsonResponse
    {
        try {
            DB::beginTransaction();
            if (! empty($eexaminee->is_hand)) {
                return $this->response()->error('重复交卷不可取.');
            }

            $eexaminee->is_hand = ExaminationExaminee::IS_HAND_OK;
            $eexaminee->hand_time = Carbon::now();
            if (empty($eexaminee->save())) {
                DB::rollBack();
                return $this->response()->error('水逆，交卷失败.');
            }

            // ... 后续处理
            MarkingObjectiveJob::dispatch($eexaminee);

            DB::commit();
            return $this->response()->success();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->response()->error($exception->getMessage());
        }
    }
}
