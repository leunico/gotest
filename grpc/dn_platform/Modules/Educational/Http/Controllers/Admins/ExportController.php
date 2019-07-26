<?php

namespace Modules\Educational\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Educational\Exports\StudyClassExport;
use Illuminate\Support\Carbon;

class ExportController extends Controller
{
    /**
     * 导出学员列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function classStudents(Request $request)
    {
        return Excel::download(new StudyClassExport($request), Carbon::now()->format('Y-m-d H:i:s') . '学员列表.xlsx');
    }
}
