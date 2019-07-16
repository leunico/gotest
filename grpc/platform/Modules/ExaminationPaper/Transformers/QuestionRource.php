<?php

namespace Modules\ExaminationPaper\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Examinee\Transformers\ExamineeAnswerResource;
use App\Http\Resources\FileResource;
use Modules\Examination\Transformers\ExaminationExamineeRource;
use App\Traits\FileHandle;
use Illuminate\Http\Resources\PotentiallyMissing;

class QuestionRource extends Resource
{
    use FileHandle;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if (! ($this->whenLoaded('esort') instanceof PotentiallyMissing) && $this->esort) {
            $this->sort = $this->esort->sort;
        }
        
        return [
            'id' => $this->id,
            'question_title' => $this->question_title,
            'category' => $this->category,
            'major_problem_id' => $this->major_problem_id,
            'score' => $this->score,
            'level' => $this->level,
            'sort' => $this->sort,
            'completion_count' => $this->completion_count,
            // 'answer' => $this->answer,
            'knowledge' => $this->knowledge,
            'code' => $this->code,
            'eexaminee' => $this->when(! empty($this->eexaminee), new ExaminationExamineeRource($this->eexaminee)),
            'code_file' => $this->code_file,
            'file_disk' => $this->getDiskUrl($this->mediaPath()),
            'options' => QuestionOptionRource::collection($this->whenLoaded('options')),
            'examinee_answer' => new ExamineeAnswerResource($this->whenLoaded('examineeAnswer')->first())
        ];
    }
}
