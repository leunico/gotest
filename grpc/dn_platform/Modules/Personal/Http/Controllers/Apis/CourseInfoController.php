<?php
/**
 * Created by PhpStorm.
 * User: MRW
 * Date: 2018/11/14
 * Time: 18:46
 */

namespace Modules\Personal\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Modules\Course\Entities\Course;
use Illuminate\Http\Request;

class CourseInfoController extends Controller
{
    public function show(Request $request){
        $course = Course::with('lessons')
            ->category($request->category)
            ->where('status',1)
            ->get();
        return $this->response()->success($course);
    }
}