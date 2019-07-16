<?php

namespace Modules\ExaminationPaper\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\ExaminationPaper\Http\Controllers\ExaminationPaperController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\ExaminationPaper\Http\Requests\StoreExaminationSimulationPaperRequest;
use Modules\ExaminationPaper\Entities\ExaminationSimulationPaper;
use Modules\Examination\Entities\ExaminationCategory;

class ExaminationSimulationPaperController extends Controller
{
    /**
     * 添加试卷.
     *
     * @param \Modules\ExaminationPaper\Http\Requests\StoreExaminationSimulationPaperRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreExaminationSimulationPaperRequest $request): JsonResponse
    {
        $simulation = ExaminationSimulationPaper::firstOrNew([
            'examination_category_id' => $request->examination_category_id
        ]);
        
        $simulation->content = $request->content;
        $simulation->creator_id = $this->user()->id;

        return $simulation->save() ? $this->response()->success($simulation) : $this->response()->error();
    }

    /**
     * 修改试卷.
     *
     * @param \Modules\ExaminationPaper\Http\Requests\StoreExaminationSimulationPaperRequest $request
     * @param \Modules\ExaminationPaper\Entities\ExaminationSimulationPaper $simulation
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreExaminationSimulationPaperRequest $request, ExaminationSimulationPaper $simulation): JsonResponse
    {
        $simulation->content = $request->content;

        return $this->response()->success($simulation->save());
    }

    /**
     * 获取一条试卷的内容
     *
     * @param \Modules\Examination\Entities\ExaminationCategory $category
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(ExaminationCategory $category): JsonResponse
    {
        return $this->response()->success(ExaminationSimulationPaper::where('examination_category_id', $category->id)->first());
    }
}
 