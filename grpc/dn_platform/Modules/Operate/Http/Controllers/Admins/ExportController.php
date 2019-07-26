<?php

namespace Modules\Operate\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Modules\Operate\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * 导出订单列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Request $request)
    {
        return Excel::download(new OrderExport($request), Carbon::now()->format('Y-m-d H:i:s') . '订单列表.xlsx');
    }
}
