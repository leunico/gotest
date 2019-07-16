<?php

namespace Modules\Examinee\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examinee\Entities\ExamineeTechnicalSupport;

class ExamineeTechnicalSupportController extends Controller
{
    /**
     * 技术支持列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Examinee\Entities\ExamineeTechnicalSupport $support
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, ExamineeTechnicalSupport $support): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);

        $examination = $request->input('examination_id', null);
        $status = $request->input('status', null);
        $keyword = $request->input('keyword', null);

        $data = $support->select(
            'examination_examinee_id',
            'examinee_technical_supports.id',
            'examinee_technical_supports.status',
            'examinee_technical_supports.description',
            'admission_ticket',
            'examinees.name',
            'phone',
            'email',
            'examinees.id as examinee_id',
            'examinations.title'
        )
            ->leftjoin('examination_examinees', 'examinee_technical_supports.examination_examinee_id', 'examination_examinees.id')
            ->leftjoin('examinees', 'examination_examinees.examinee_id', 'examinees.id')
            ->leftjoin('examinations', 'examination_examinees.examination_id', 'examinations.id')
            ->when(! is_null($status), function ($query) use ($status) {
                $query->where('examinee_technical_supports.status', $status);
            })
            ->when(! empty($examination), function ($query) use ($examination) {
                $query->where('examinations.id', $examination);
            })
            ->when(! is_null($keyword), function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%")
                        ->orWhere('certificates', 'like', "%$keyword%")
                        ->orWhere('phone', 'like', "%$keyword%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $statistics = ExamineeTechnicalSupport::select('id', 'status')->get();
        return $this->response()->success($data, [
            'status_ok' => $statistics->where('status', ExamineeTechnicalSupport::STATUS_OK)->count(),
            'status_off' => $statistics->where('status', ExamineeTechnicalSupport::STATUS_OFF)->count(),
            'status_in' => $statistics->where('status', ExamineeTechnicalSupport::STATUS_IN)->count(),
        ]);
    }

    /**
     * 技术支持更改状态
     *
     * @param \Modules\Examinee\Entities\ExamineeTechnicalSupport $support
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function status(ExamineeTechnicalSupport $support): JsonResponse
    {
        if ($support->status != ExamineeTechnicalSupport::STATUS_OK) {
            $support->status = $support->status + 1;
        }

        return $this->response()->success($support->save());
    }
}
