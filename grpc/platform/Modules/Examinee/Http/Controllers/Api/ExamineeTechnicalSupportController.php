<?php

namespace Modules\Examinee\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examinee\Http\Requests\ExamineeTechnicalSupportRequest;
use Modules\Examination\Entities\ExaminationExaminee;
use Modules\Examinee\Entities\ExamineeTechnicalSupport;

class ExamineeTechnicalSupportController extends Controller
{
    /**
     * 添加技术支持.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeTechnicalSupportRequest $request
     * @param \Modules\Examinee\Entities\ExamineeTechnicalSupport $support
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(ExamineeTechnicalSupportRequest $request, ExaminationExaminee $eexaminee, ExamineeTechnicalSupport $support): JsonResponse
    {
        $support->examination_examinee_id = $eexaminee->id;
        $support->description = $request->description;

        return $support->save() ? $this->response()->success() :$this->response()->error();
    }
}
