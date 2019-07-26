<?php

namespace Modules\Educational\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Educational\Http\Requests\StoreHolidayPost;
use Modules\Educational\Entities\Holiday;
use function App\responseSuccess;
use function App\responseFailed;

class HolidayController extends Controller
{
    /**
     * 查看节假日列表
     *
     * @param \Modules\Educational\Entities\Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function index(Holiday $holiday)
    {
        return responseSuccess($holiday->all());
    }

    /**
     * 添加一个节假日
     *
     * @param \Modules\Educational\Http\Requests\StoreHolidayPost $request
     * @param \Modules\Educational\Entities\Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHolidayPost $request, Holiday $holiday)
    {
        $holiday->date = $request->date;
        $holiday->name = $request->input('name', '');
        $holiday->describe = $request->input('describe', '');

        if ($holiday->save()) {
            return responseSuccess([
                'holiday_id' => $holiday->id
            ], '添加节假日成功');
        } else {
            return responseFailed('添加节假日失败', 500);
        }
    }

    /**
     * 删除日期
     *
     * @param \Modules\Educational\Entities\Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return responseSuccess();
    }
}
