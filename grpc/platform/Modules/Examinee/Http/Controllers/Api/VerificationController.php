<?php

namespace Modules\Examinee\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use App\Factories\TencentCloud\LivenessRecognition;
use Illuminate\Http\JsonResponse;
use Modules\Examinee\Http\Requests\ExamineeTencentFaceRequest;
use Modules\Examinee\Entities\ExamineeTencentFace;
use Modules\Examination\Entities\ExaminationExaminee;
use App\Factories\TencentCloud\LivenessCompare;
use App\Factories\Face\FacePlusPlusAnalysis;
use Modules\Examinee\Entities\FaceUser;
use Illuminate\Support\Facades\DB;
use Modules\Examinee\Entities\FaceAnalysis;
use App\Factories\Face\Face;
use Modules\Examinee\Entities\ExamineeVideo;
use Modules\Examinee\Http\Requests\ExamineeVideoRequest;

class VerificationController extends Controller
{
    /**
     * 考前人脸验证.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeTencentFaceRequest $request
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examinee\Entities\ExamineeTencentFace $face
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function guard(ExamineeTencentFaceRequest $request, ExaminationExaminee $eexaminee, ExamineeTencentFace $face): JsonResponse
    {
        $faceResp = ($this->examinee()->isCertificateTypeOne() ? new LivenessRecognition : new LivenessCompare)->handle($request->video, $this->examinee());
        if (! isset($faceResp['RequestId']) || empty($faceResp['RequestId'])) {
            return $this->response()->error('Request Error.');
        }

        $face->examination_examinee_id = $eexaminee->id;
        $face->request_id = $faceResp['RequestId'];
        $face->description = $faceResp['Description'];
        $face->sim = $faceResp['Sim'];
        $face->result = $faceResp['Result'];
        $face->best_file = isset($faceResp['BestFile']) ? $faceResp['BestFile']->id : 0;
        $face->type = $request->type;
        $face->category = $this->examinee()->isCertificateTypeOne() ? ExamineeTencentFace::CATEGORY_RECOGNITION : ExamineeTencentFace::CATEGORY_COMPARE;
        $face->save();

        if ($faceResp['Result'] != 'Success') {
            return $this->response()->errorUnprocessableEntity($faceResp['Description']);
        }

        if ($faceResp['Sim'] < 70) { // 人脸比对置信度
            return $this->response()->errorUnprocessableEntity('验证失败，不是本人或者请摆正姿势，全脸放入视频框内！');
        }

        return $this->response()->success([
            'token' => $faceResp['RequestId']
        ]);
    }

    /**
     * 考试时face++认证.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \App\Factories\Face\FacePlusPlusAnalysis $analysis
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function analysis(Request $request, ExaminationExaminee $eexaminee, FacePlusPlusAnalysis $faceAnalysis): JsonResponse
    {
        $this->validate($request, ['image_file' => 'required|file|mimes:jpeg,png|max:2097152']);
        try {
            DB::beginTransaction();
            $detect = $faceAnalysis->detect($request->image_file);

            if (isset($detect['error_message'])) {
                return $this->response()->error($detect);
            }

            $faceUser = FaceUser::firstOrNew(['request_id' => $detect['request_id']], [
                'examination_examinee_id' => $eexaminee->id,
                'file_id' => $detect['file_id'],
            ]);

            if (empty($detect['faces'])) {
                $faceUser->status = Face::FACE_ANALYSIS_NOTFOUNT;
                $faceUser->save();
                DB::commit();
                return $this->response()->errorUnprocessableEntity(Face::$tips[Face::FACE_ANALYSIS_NOTFOUNT], ['status' => Face::FACE_ANALYSIS_NOTFOUNT]);
            } elseif (count($detect['faces']) > 1) {
                $faceUser->status = Face::FACE_ANALYSIS_MANY;
                $faceUser->save();
                DB::commit();
                return $this->response()->errorUnprocessableEntity(Face::$tips[Face::FACE_ANALYSIS_MANY], ['status' => Face::FACE_ANALYSIS_MANY]);
            }

            $analysis = new FaceAnalysis;
            $faces = array_pop($detect['faces']);
            $analysis->face_token = $faces['face_token'];
            $analysis->image_id = $detect['image_id'];
            $analysis->gender = $faces['attributes']['gender']['value'];
            $analysis->age = $faces['attributes']['age']['value'];
            $analysis->headpose = $faces['attributes']['headpose'];
            $analysis->blur = $faces['attributes']['blur'];
            $analysis->eyegaze_left = $faces['attributes']['eyegaze']['left_eye_gaze'];
            $analysis->eyegaze_right = $faces['attributes']['eyegaze']['right_eye_gaze'];
            if ($analysis->save()) {
                $compare = $faceAnalysis->compare($analysis->face_token, $this->examinee()->photo);
                $faceUser->face_analysis_id = $analysis->id;
                if (isset($compare['confidence'])) {
                    $faceUser->confidence = $compare['confidence'];
                    if ($compare['confidence'] < 70) { // 人脸比对置信度
                        $faceUser->status = Face::FACE_ANALYSIS_COMPARE;
                    }
                }
                $faceUser->save();
                DB::commit();
                if ($faceUser->status == Face::FACE_ANALYSIS_COMPARE) {
                    return $this->response()->errorUnprocessableEntity(Face::$tips[Face::FACE_ANALYSIS_COMPARE], ['status' => Face::FACE_ANALYSIS_COMPARE]);
                }
                if (isset($compare['error_message'])) {
                    return $this->response()->error($compare);
                }
                return $this->response()->success(['request_id' => $detect['request_id']]);
            }

            DB::rollBack();
            return $this->response()->error('数据异常');
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception->getTraceAsString());
            return $this->response()->error($exception->getMessage());
        }
    }

    /**
     * 考试录像.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeVideoRequest $request
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examinee\Entities\ExamineeVideo $video
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function video(ExamineeVideoRequest $request, ExaminationExaminee $eexaminee, ExamineeVideo $video): JsonResponse
    {
        $video->examination_examinee_id = $eexaminee->id;
        $video->video_url = $request->video_url;
        $video->type = $request->type;

        return $this->response()->success($video->save());
    }
}
