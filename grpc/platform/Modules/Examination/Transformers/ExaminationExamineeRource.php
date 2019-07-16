<?php

namespace Modules\Examination\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Carbon;
use Modules\Examinee\Entities\ExamineeTencentFace;
use Modules\Examinee\Transformers\ExamineeDeviceProbingResource;
use Modules\Examinee\Transformers\ExamineeTencentFaceResource;
use Modules\Examination\Entities\Examination;
use Modules\Examinee\Transformers\ExamineeVideoResource;
use Modules\Examinee\Transformers\ExamineeOperationResource;

class ExaminationExamineeRource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $parseStart = Carbon::parse($this->start_at);
        $parseEnd = Carbon::parse($this->end_at);
        if (Carbon::now()->lt($parseStart)) {
            $examinationStatus = 0;
        } elseif (Carbon::now()->gte($parseStart) && Carbon::now()->lte($parseEnd)) {
            $examinationStatus = 1;
        } elseif (Carbon::now()->gt($parseEnd)) {
            $examinationStatus = 2;
        }

        return [
            'id' => $this->id,
            'examination_id' => $this->examination_id,
            'examinee_id' => $this->examinee_id,
            'examination_category_id' => $this->examination_category_id,
            'title' => $this->title,
            'examination_paper_title' => $this->examination_paper_title,
            'start_time' => $this->start_time,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'exam_file_url' => $this->driver_baseurl . $this->filename,
            'examination_status' => $examinationStatus ?? -1,
            'is_hand' => $this->is_hand,
            'status' => $this->status,
            'admission_ticket' => $this->admission_ticket,
            'total_score' => $this->total_score,
            'achievement_status' => $this->achievement_status,
            'achievements' => $this->when($this->status == Examination::STATUS_ACHIEVEMENT, 
                ['objective_score' => $this->objective_score, 'subjective_score' => $this->subjective_score, 'rank' => $this->rank]),
            'testing_status' => $this->testing_status,
            'examinee_tencent_face_testing' => $this->when($request->routeIs('examinee-examination-detail'), function () {
                return $this->examineeTencentFaces ? 
                    $this->examineeTencentFaces
                        ->where('type', ExamineeTencentFace::TYPE_BEFORE)
                        ->where('result', 'Success')
                        ->isNotEmpty() : false;
            }),
            'examinee_device_probings' => ExamineeDeviceProbingResource::collection($this->whenLoaded('examineeDeviceProbings') ),
            'examinee_tencent_faces' => ExamineeTencentFaceResource::collection($this->whenLoaded('examineeTencentFaces')),
            'examinee_videos' => ExamineeVideoResource::collection($this->whenLoaded('examineeVideos')),
            'examinee_operations' => ExamineeOperationResource::collection($this->whenLoaded('examineeOperations')),
        ];
    }
}
