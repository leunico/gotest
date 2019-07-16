<?php

namespace Modules\ExaminationPaper\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\ExaminationPaper\Http\Controllers\ExaminationPaperController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Modules\Examination\Entities\ExaminationCategory;
use Modules\ExaminationPaper\Transformers\ExaminationSimulationPaperResource;
use Modules\ExaminationPaper\Entities\ExaminationSimulationPaper;

class ExaminationSimulationPaperController extends Controller
{
    /**
     * 获取一条试卷的内容
     *
     * @param \Modules\Examination\Entities\ExaminationCategory $category
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(ExaminationCategory $category): JsonResponse
    {
        return $this->response()->item(
            ExaminationSimulationPaper::where('examination_category_id', $category->id)->first(), 
            ExaminationSimulationPaperResource::class
        );
    }
}
