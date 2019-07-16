<?php

namespace Modules\Examination\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examination\Http\Controllers\ExaminationController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examination\Entities\Examination;
use Modules\Examination\Http\Requests\StoreExaminationRequest;
use function App\toDecbin;
use Illuminate\Support\Carbon;
use Modules\Examination\Jobs\ExaminationExamineeRankJob;
use Illuminate\Support\Facades\DB;
use Modules\Examinee\Entities\ExamineeOperation;
use Modules\Examination\Entities\ExaminationExaminee;

class ExaminationController extends Controller
{
    /**
     * 考试列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $matchId
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, int $matchId = 0): JsonResponse
    {
        $category = $request->input('category', null);
        $subject = $request->input('subject', null);
        $status = $request->input('status', null);
        $process = $request->input('process', null);
        $roles = $request->input('roles', null);
        $cheat = $request->input('cheat', null);
        $marking = $request->input('marking', null);
        $qualification = $request->input('qualification', null);
        $startTime = $request->input('start_time', 0);
        $endTime = $request->input('end_time', 0);
        $staff = $request->input('staff', 0);

        $data = Examination::select(
            'examinations.id',
            'examinations.title',
            'start_at',
            'end_at',
            'release_user_id',
            'release_time',
            'qualification_user_id',
            'qualification_time',
            'examination_categories.title as category_title',
            'category',
            'examinations.created_at',
            'examination_paper_title',
            'status',
            'examinations.creator_id'
        )
            ->leftjoin('examination_categories', 'examinations.examination_category_id', 'examination_categories.id')
            ->when(! $this->user()->isSuperAdmin() && ! $this->user()->hasRole('exam_organization'), function ($query) use ($staff) {
                $query->leftjoin('examination_users', 'examinations.id', 'examination_users.examination_id')
                    ->where('examination_users.user_id', $this->user()->id)
                    ->where(DB::raw("examination_users.type & $staff"), $staff);
            })
            ->when(! empty($matchId), function ($query) use ($matchId) {
                $query->where('match_id', $matchId);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                $query->where('examinations.status', $status);
            })
            ->when(is_null($status) && ! is_null($process), function ($query) use ($process) {
                $query->where('examinations.status', '>=', $process);
            })
            ->when(! is_null($category), function ($query) use ($category) {
                $query->where('examination_categories.id', $category);
            })
            ->when(! is_null($subject), function ($query) use ($subject) {
                $query->where('category', $subject);
            })
            ->when(! is_null($qualification), function ($query) use ($qualification) {
                switch ($qualification) {
                    case 1:
                        $query->whereNotNull('qualification_time');
                        break;
                    case 2:
                        $query->where('end_at', '<', Carbon::now())
                            ->whereNull('qualification_time');
                        break;
                    default:
                        $query->where('end_at', '>=', Carbon::now());
                        break;
                }
            })
            ->when((! empty($startTime) || ! empty($endTime)), function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_at', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfday()]);
            })
            ->when(! is_null($roles), function ($query) use ($roles) {
                $query->with(['examinationStaff' => function ($q) use ($roles) {
                    empty($roles) ? $q->select('name', 'real_name', 'user_id') :
                        $q->select('name', 'real_name', 'user_id', 'type')->where(DB::raw("type & $roles"), $roles);
                }]);
            })
            ->when(! empty($cheat), function ($query) {
                $query->withCount([
                    'examinationExaminees as examinationExamineesCheat' => function ($query) {
                        $query->whereHas('examineeOperations', function ($q) {
                            $q->where('category', '>', ExamineeOperation::CATEGORY_NOTHING);
                        }, '>=', 3);
                    }
                ]);
            })
            ->when(! empty($marking), function ($query) {
                $query->withCount([
                    'examinationExaminees as examinationExamineesMarking' => function ($query) {
                        $query->where('is_hand', ExaminationExaminee::IS_HAND_OK)
                            ->where('achievement_status', ExaminationExaminee::ACHIEVEMENT_STATUS_OK);
                    }
                ]);
            })
            ->with([
                'creator:id,name,real_name',
                'statusUser:id,name,real_name',
                'qualificationUser:id,name,real_name',
                'majorProblems:id,title,examination_id,total_score'
            ])
            ->withCount(['examinationExaminees'])
            ->get();

        return $this->response()->success($data);
    }

    /**
     * 添加考试.
     *
     * @param \Modules\Examination\Http\Requests\StoreExaminationRequest $request
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreExaminationRequest $request, Examination $examination): JsonResponse
    {
        $examination->creator_id = $this->user()->id;
        $examination->examination_category_id = $request->examination_category_id;
        $examination->match_id = $request->match_id;
        $examination->title = $request->title;
        $examination->examination_paper_title = $request->examination_paper_title;
        $examination->start_at = $request->start_at;
        $examination->end_at = $request->end_at;
        $examination->age_min = $request->age_min;
        $examination->age_max = $request->age_max;
        $examination->description = $request->input('description', '');
        $examination->exam_file_id = $request->exam_file_id;

        $examination->getConnection()->transaction(function () use ($examination, $request) {
            if ($examination->save()) {
                $examination->examinationStaff()->attach(array_map(function ($item) {
                    return ['type' => array_sum($item)];
                }, $request->staffs));
            }
        });

        return $this->response()->success($examination);
    }

    /**
     * 获取考试.
     *
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Examination $examination): JsonResponse
    {
        $examination->load([
            'examFile:id,origin_filename,driver_baseurl,filename',
            'category:id,category,title',
            'examinationStaff:users.id,name,real_name,type',
            'majorProblems' => function ($query) {
                $query->orderBy('sort')
                    ->select('id', 'sort', 'title', 'description', 'examination_id', 'total_score', 'is_question_disorder', 'is_option_disorder');
            },
            'majorProblems.questions' => function ($query) {
                $query->orderBy('sort');
            },
            'majorProblems.questions.options' => function ($query) {
                $query->orderBy('sort');
            },
        ]);

        $examination->examinationStaffs = Examination::$staffs;
        $examination->examinationStaff->map(function ($item) {
            $item->type = toDecbin($item->type);
        });

        return $this->response()->success($examination);
    }

    /**
     * 修改考试.
     *
     * @param \Modules\Examination\Http\Requests\StoreExaminationRequest $request
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreExaminationRequest $request, Examination $examination): JsonResponse
    {
        $examination->examination_category_id = $request->examination_category_id;
        $examination->title = $request->title;
        $examination->examination_paper_title = $request->examination_paper_title;
        $examination->start_at = $request->start_at;
        $examination->end_at = $request->end_at;
        $examination->age_min = $request->age_min;
        $examination->age_max = $request->age_max;
        $examination->description = $request->input('description', '');
        $examination->exam_file_id = $request->exam_file_id;

        $examination->getConnection()->transaction(function () use ($examination, $request) {
            if ($examination->save()) {
                $examination->examinationStaff()->sync(array_map(function ($item) {
                    return ['type' => array_sum($item)];
                }, $request->staffs));
            }
        });

        return $this->response()->success($examination);
    }

    /**
     * 考试资格确认.
     *
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function setQualification(Examination $examination): JsonResponse
    {
        if (Carbon::now()->lte(Carbon::parse($examination->end_at))) {
            return $this->response()->error('当前考试状态错误：考试未结束，不可确认！');
        }

        $examination->qualification_user_id = $this->user()->id;
        $examination->qualification_time = Carbon::now();

        return $this->response()->success($examination->save());
    }

    /**
     * 考试发布/取消发布.
     *
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function setStatus(Examination $examination): JsonResponse
    {
        if ($examination->status != Examination::STATUS_PAPER) {
            return $this->response()->error('当前考试状态错误：不是考卷发布状态，请完善试卷！');
        }

        $examination->status = Examination::STATUS_EXAMINATION;

        return $this->response()->success($examination->save());
    }

    /**
     * 考卷发布.
     *
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function setPaper(Examination $examination): JsonResponse
    {
        $examination->status = Examination::STATUS_PAPER;
        $examination->release_user_id = $this->user()->id;
        $examination->release_time = Carbon::now();

        return $this->response()->success($examination->save());
    }

    /**
     * 成绩发布.
     *
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sePublishResults(Examination $examination): JsonResponse
    {
        if ($examination->status != Examination::STATUS_EXAMINATION) {
            return $this->response()->error('当前考试状态错误：不是考试发布状态！');
        }

        $examination->load([
            'examinationExaminees' => function ($query) {
                $query->select('id', 'examination_id', 'is_hand', 'achievement_status')
                    ->where('is_hand', ExaminationExaminee::IS_HAND_OK)
                    ->where('achievement_status', ExaminationExaminee::ACHIEVEMENT_STATUS_OK);
            }
        ]);

        if ($examination->examinationExaminees->isNotEmpty() || empty($examination->qualification_time)) {
            return $this->response()->error('该考试还有考生未阅卷或该考试还未确认考生考试资格！');
        }

        $examination->status = Examination::STATUS_ACHIEVEMENT;
        if ($status = $examination->save()) {
            ExaminationExamineeRankJob::dispatch($examination);
        }

        return $this->response()->success($status);
    }

    /**
     * 删除考试.
     *
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(Examination $examination): JsonResponse
    {
        if ($examination->majorProblems->isNotEmpty()) {
            return $this->response()->error('删除错误，考试已有考卷数据！');
        }

        return $this->response()->success($examination->delete());
    }
}
