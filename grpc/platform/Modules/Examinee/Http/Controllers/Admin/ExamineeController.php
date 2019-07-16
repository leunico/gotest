<?php

namespace Modules\Examinee\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examinee\Entities\Examinee;
use Modules\Examinee\Http\Requests\StoreExamineeRequest;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Modules\Examination\Entities\ExaminationExaminee;
use App\Rules\ArrayExists;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Examinee\Exports\ExamineeTemplateExport;
use Modules\Examinee\Imports\ExamineeImport;
use Illuminate\Validation\Rule;
use Modules\Examinee\Emails\ExamineeTestingRemind;
use Illuminate\Support\Facades\Mail;

class ExamineeController extends Controller
{
    /**
     * 考生列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $examinationId
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, int $examinationId = 0): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = (int) $request->input('is_all', null);

        $keyword = $request->input('keyword', null);
        $test = $request->input('test', null);
        $status = $request->input('status', null);
        $estatus = $request->input('examinee_status', null);
        $examination = $request->input('examination_id', null);

        $query = Examinee::select('id', 'name', 'certificates', 'certificate_type', 'contacts', 'email', 'phone', 'creator_id', 'created_at', 'examinees.status')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%")
                        ->orWhere('certificates', 'like', "%$keyword%")
                        ->orWhere('email', 'like', "%$keyword%")
                        ->orWhere('phone', 'like', "%$keyword%");
                });
            })
            ->when(! is_null($estatus), function ($query) use ($estatus) {
                $query->where('examinees.status', $estatus);
            })
            ->when(! empty($examinationId), function ($query) use ($examinationId, $test, $status) {
                $query->rightjoin('examination_examinees', 'examinees.id', 'examination_examinees.examinee_id')
                    ->select(
                        'examinees.id',
                        'name',
                        'certificates',
                        'certificate_type',
                        'contacts',
                        'email',
                        'phone',
                        'examination_examinees.status',
                        'creator_id',
                        'examinees.created_at',
                        'examination_examinees.id as examination_examinee_id',
                        'admission_ticket',
                        'testing_status'
                    )
                    ->when(! is_null($test), function ($query) use ($test) {
                        $query->where('testing_status', $test);
                    })
                    ->when(! is_null($status), function ($query) use ($status) {
                        $query->where('examination_examinees.status', $status);
                    })
                    ->withCount([
                        'examineeExaminationTestingPushs' => function ($query) use ($examinationId) {
                            $query->where('pushtable_id', $examinationId);
                        }
                    ])
                    ->whereNull('examination_examinees.deleted_at')
                    ->where('examination_id', $examinationId);
            }, function ($query) use ($examination) {
                $query->with([
                    'examinations:examinations.id,title'
                ])
                ->when(! is_null($examination), function ($q) use ($examination) {
                    $q->whereIn('id', function ($query) use ($examination) {
                        $query->select('examinee_id')
                            ->from('examination_examinees')
                            ->where('examination_id', $examination);
                    });
                });
            })
            ->orderBy('id', 'desc')
            ->with([
                'creator:id,name,real_name'
            ]);

        return $this->response()->success(
            empty($isAll) ? $query->paginate($perPage) : $query->get(),
            (new ExaminationExaminee)->getExaminationtaStatistics($examinationId)
        );
    }

    /**
     * 创建考生.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examinee\Http\Requests\StoreExamineeRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreExamineeRequest $request, ExaminationExaminee $eexaminee): JsonResponse
    {
        $examinee = Examinee::firstOrNew(['certificates' => $request->certificates])
                ->load([
                    'examinationPivots:id,examinee_id,examination_id,status'
                ]);
        if ($examinee->examinationPivots->isNotEmpty() && 
            $examinee->examinationPivots->where('status', ExaminationExaminee::STATUS_OK)->contains('examination_id', $request->examination_id)) {
            return $this->response()->errorUnprocessableEntity('证件号已存在，并且已确认！');
        }
        
        $examinee->phone = $request->phone;
        $examinee->email = $request->email;
        $examinee->name = $request->name;
        $examinee->certificate_type = $request->certificate_type;
        $examinee->creator_id = $this->user()->id;
        $examinee->source = Examinee::SOURCE_LR;
        $examinee->sex = $request->sex;
        $examinee->contacts = $request->contacts;
        $examinee->birth = $request->birth;
        $examinee->photo = $request->photo;
        $examinee->certificates_photos = json_encode([$request->certificates_photos_a, $request->certificates_photos_b]);
        $examinee->remarks = $request->input('remarks', '');
        $examinee->school_name = $request->input('school_name', '');
        $examinee->city = $request->input('city', 0);
        $examinee->password = $examinee->password ?? Hash::make(substr($request->certificates, -6));
        $examinee->getConnection()->transaction(function () use ($examinee, $request, $eexaminee) {
            if ($examinee->save() &&
                $request->examination_id && 
                ! $examinee->examinationPivots->contains('examination_id', $request->examination_id)) {
                $examinee->examinations()->attach($request->examination_id, [
                    'admission_ticket' => $eexaminee->getOnlyAdmissionTicket($request->examination_id)
                ]);
            }
        });

        return $this->response()->success($examinee);
    }

    /**
     * 获取考生.
     *
     * @param \Modules\Examinee\Entities\Examinee $examinee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Examinee $examinee): JsonResponse
    {
        $examinee->load([
            'creator:id,name,real_name',
            'examinationPivots:id,examinee_id,examination_id,admission_ticket,testing_status'
        ]);

        return $this->response()->success($examinee);
    }

    /**
     * 获取考生导入模板.
     *
     * @return void
     * @author lizx
     */
    public function template()
    {
        return Excel::download(new ExamineeTemplateExport(), '考生导入模板.xlsx');
    }

    /**
     * Excel创建考生.
     *
     * @param \Modules\Examinee\Entities\Examinee $examinee
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function excel(Request $request, Examinee $examinee, ExaminationExaminee $eexaminee): JsonResponse
    {
        // dd($request->file('excel_file')->guessExtension());
        $this->validate($request, [
            'excel_file' => 'required|file|mimes:xlsx,csv,bin',
            'examination_id' => [
                'integer',
                Rule::exists('examinations', 'id')->whereNull('deleted_at')
            ]
        ]);

        Excel::import(new ExamineeImport($eexaminee, (int) $request->input('examination_id', 0)), $request->file('excel_file'));

        return $this->response()->success();
    }

    /**
     * 批量确认
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function allStatus(Request $request, ExaminationExaminee $eexaminee): JsonResponse
    {
        $this->validate($request, ['ids' => [
            'required',
            'array',
            new ArrayExists($eexaminee)
        ]]);

        $eexaminee->getConnection()->transaction(function () use ($eexaminee, $request) {
            if ($eexaminee->whereIn('id', $request->ids)
            ->update([
                'status' => Examinee::STATUS_OK
            ])) {
                $eexaminee->whereIn('id', $request->ids)
                    ->with('examinee:id,certificates,name,email')
                    ->get()
                    ->map(function ($item) {
                        Mail::to($item->examinee)->queue(new ExamineeTestingRemind($item));
                    });
            }
        });

        return $this->response()->success();
    }

    /**
     * 确认取消
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function status(ExaminationExaminee $eexaminee): JsonResponse
    {
        $eexaminee->status = ! $eexaminee->status;
        $eexaminee->save();

        if (! empty($eexaminee->status)) {
            Mail::to(Examinee::find($eexaminee->examinee_id))->queue(new ExamineeTestingRemind($eexaminee));
        }

        return $this->response()->success($eexaminee);
    }

    /**
     * 推送考生检测
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function pushTesting(ExaminationExaminee $eexaminee): JsonResponse
    {
        Mail::to(Examinee::find($eexaminee->examinee_id))->queue(new ExamineeTestingRemind($eexaminee));

        return $this->response()->success();
    }

    /**
     * 编辑考生.
     *
     * @param \Modules\Examinee\Entities\Examinee $examinee
     * @param \Modules\Examinee\Http\Requests\StoreExamineeRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreExamineeRequest $request, Examinee $examinee): JsonResponse
    {
        $examinee->phone = $request->phone;
        $examinee->email = $request->email;
        $examinee->name = $request->name;
        $examinee->certificates = $request->certificates;
        $examinee->certificate_type = $request->certificate_type;
        $examinee->sex = $request->sex;
        $examinee->contacts = $request->contacts;
        $examinee->birth = $request->birth;
        $examinee->photo = $request->photo;
        $examinee->certificates_photos = json_encode([$request->certificates_photos_a, $request->certificates_photos_b]);
        $examinee->remarks = $request->input('remarks', '');
        $examinee->school_name = $request->input('school_name', '');
        $examinee->city = $request->input('city', 0);
        $examinee->password = $request->password ? Hash::make($request->password) : $examinee->password;
        $examinee->save();

        return $this->response()->success($examinee);
    }

    /**
     * 删除考生.
     *
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @return Response
     * @author lizx
     */
    public function destroy(ExaminationExaminee $eexaminee): JsonResponse
    {
        if (! empty($eexaminee->status) && ! $this->user()->isSuperAdmin()) {
            return $this->response()->error('删除错误，考生已经确认了！');
        }

        return $this->response()->success($eexaminee->delete());
    }
}