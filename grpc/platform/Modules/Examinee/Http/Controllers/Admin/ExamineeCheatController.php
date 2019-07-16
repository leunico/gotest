<?php

namespace Modules\Examinee\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examination\Entities\ExaminationExaminee;
use Modules\Examination\Entities\Examination;
use Modules\Examinee\Entities\ExamineeOperation;
use App\Models\LoginLog;
use Modules\Examinee\Entities\ExamineeDeviceProbing;
use Modules\Examinee\Entities\FaceUser;
use App\Factories\Face\Face;
use Modules\Examinee\Entities\ExamineeTencentFace;
use Modules\Examinee\Entities\ExamineeVideo;

class ExamineeCheatController extends Controller
{
    /**
     * 考生反作弊列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examination\Entities\Examination $examination
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, Examination $examination, ExaminationExaminee $eexaminee): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);

        $keyword = $request->input('keyword', null);
        $isHand = $request->input('is_hand', null);
        $achievement = $request->input('achievement', null);

        $data = $eexaminee->select(
            'examination_examinees.id',
            'examinee_id',
            'examination_id',
            'admission_ticket',
            'is_hand',
            'hand_time',
            'objective_score',
            'subjective_score',
            'rank',
            'achievement_status',
            'name'
        )
            ->leftjoin('examinees', 'examination_examinees.examinee_id', 'examinees.id')
            ->where('examination_id', $examination->id)
            ->where('examination_examinees.status', ExaminationExaminee::STATUS_OK)
            ->when(! is_null($keyword), function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%")
                        ->orWhere('certificates', 'like', "%$keyword%")
                        ->orWhere('admission_ticket', 'like', "%$keyword%")
                        ->orWhere('phone', 'like', "%$keyword%")
                        ->orWhere('email', 'like', "%$keyword%");
                });
            })
            ->when(! is_null($isHand), function ($query) use ($isHand) {
                $query->where('is_hand', $isHand);
            })
            ->when(! is_null($achievement), function ($query) use ($achievement) {
                empty($achievement) ? $query->where('achievement_status', $achievement) : 
                    $query->whereIn('achievement_status', [ExaminationExaminee::ACHIEVEMENT_STATUS_MARKING, ExaminationExaminee::ACHIEVEMENT_STATUS_OK]);
            })
            ->with([
                'examineeOperations:id,category,examination_examinee_id,remark'
            ])
            ->withCount([
                'examineeVideos' => function ($query) {
                    $query->where('type', ExamineeVideo::TYPE_VERIFICATION);
                }
            ])
            ->paginate($perPage);

        collect($data->items())->map(function ($item) {
            $item->cutting_screen_count = $item->examineeOperations->isNotEmpty() ?
                $item->examineeOperations->where('category', ExamineeOperation::CATEGORY_CUTTING_SCREEN)->count() : 0;
            $item->face_abnormal_count = $item->examineeOperations->isNotEmpty() ?
                $item->examineeOperations->where('category', ExamineeOperation::CATEGORY_FACE)->count() : 0;
            $item->off_line_count = $item->examineeOperations->isNotEmpty() ?
                $item->examineeOperations->where('category', ExamineeOperation::CATEGORY_OFFLINE)->count() : 0;
        });

        return $this->response()->success($data, $eexaminee->getExaminationtaStatistics($examination->id));
    }

    /**
     * 作弊确认取消
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function status(ExaminationExaminee $eexaminee): JsonResponse
    {
        $eexaminee->achievement_status = ! $eexaminee->achievement_status;

        return $this->response()->success($eexaminee->save());
    }

    /**
     * 作弊操作详情.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function showLogs(ExaminationExaminee $eexaminee): JsonResponse
    {
        $deviceProbings = ExamineeDeviceProbing::where('examination_examinee_id', $eexaminee->id)->get();
        $loginLogs = LoginLog::select('id', 'examination_examinee_id', 'created_at', 'user_agent', 'city')
            ->where('examination_examinee_id', $eexaminee->id)
            ->get();

        $tencentFaces = ExamineeTencentFace::select('id', 'examination_examinee_id', 'created_at', 'sim', 'description', 'type', 'category')
            ->where('examination_examinee_id', $eexaminee->id)
            ->get();

        $offLines = ExamineeOperation::select('id', 'examination_examinee_id', 'created_at', 'remark')
            ->where('examination_examinee_id', $eexaminee->id)
            ->where('category', ExamineeOperation::CATEGORY_OFFLINE)
            ->get();

        $faceUser = FaceUser::select('id', 'examination_examinee_id', 'created_at', 'confidence', 'status')
            ->where('examination_examinee_id', $eexaminee->id)
            ->where('status', '>', FaceUser::STATUS_NORMAL)
            ->get()
            ->map(function ($item) {
                $item->status_str = Face::$tips[$item->status] ?? '';
                return $item;
            });
            
        return $this->response()->success(collect([
                $deviceProbings, 
                $loginLogs, 
                $offLines, 
                $tencentFaces, 
                $faceUser, 
                [["created_at" => $eexaminee->start_time, 'describe' => '开始考试']], 
                [["created_at" => $eexaminee->hand_time, 'describe' => '交卷时间']]
            ])
            ->collapse()
            ->sortBy('created_at')
            ->values()
        ); // compact('loginLogs', 'tencentFaces', 'deviceProbings', 'faceUser', 'eexaminee', 'offLines')
    }

    /**
     * 作弊人脸识别详情.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function showFaces(ExaminationExaminee $eexaminee): JsonResponse
    {
        $eexaminee->load([
            'faceUsers:id,examination_examinee_id,created_at,confidence,status,file_id',
            'faceUsers.file:id,driver_baseurl,filename'
        ]);

        $eexaminee->faceUsers->map(function ($item) {
            $item->status_str = Face::$tips[$item->status] ?? '无异常';
        });

        return $this->response()->success($eexaminee);
    }

    /**
     * 作弊考生录像详情.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function showVideos(ExaminationExaminee $eexaminee): JsonResponse
    {
        $eexaminee->load([
            'examinee:id,photo,name',
            'examineeVideos:id,examination_examinee_id,created_at,video_url,type'
        ]);

        return $this->response()->success($eexaminee);
    }
}
