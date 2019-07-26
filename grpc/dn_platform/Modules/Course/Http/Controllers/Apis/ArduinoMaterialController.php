<?php

declare(strict_types=1);

namespace Modules\Course\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Course\Entities\ArduinoMaterial;
use App\Http\Controllers\Concerns\ControllerExtend;
use App\Http\Controllers\Controller;
use Modules\Course\Transformers\ArduinoMaterialResource;
use Modules\Personal\Entities\CourseUser;
use Illuminate\Http\JsonResponse;
use Modules\Course\Entities\Course;
use Modules\Course\Entities\BigCourse;
use Modules\Course\Entities\BigCourseCoursePivot;

class ArduinoMaterialController extends Controller
{
    use ControllerExtend;

    /**
     * 获取arduino素材列表[前端]
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function arduinos(Request $request, Course $course): JsonResponse
    {
        $data = collect([]);
        $thisBigCourse = $request->big_course ? BigCourseCoursePivot::select('sort', 'course_id')
            ->where('big_course_id', $request->big_course)
            ->get()
            ->keyBy('course_id') : collect([]);
        CourseUser::where('user_id', request()->user()->id)
            ->select('course_id')
            ->where('status', CourseUser::STATUS_NO)
            ->with([
                'course' => function ($query) use ($course, $thisBigCourse) {
                    $sort = $thisBigCourse->get($course->id);
                    $query->select('id', 'title', 'level')
                        ->when($thisBigCourse->isEmpty() || ! $sort, function ($query) use ($course) {
                            $query->where('id', '<=', $course->id);
                        }, function ($query) use ($thisBigCourse, $sort) {
                            $query->whereIn('id', $thisBigCourse->reject(function ($value) use ($sort) {
                                return $value->sort > $sort->sort;
                            })->keys());
                        });
                },
                'course.arduinos' => function ($query) {
                    $query->select('arduino_materials.id', 'name', 'md5', 'info', 'is_arduino', 'source_link')
                        ->orderBy('arduino_materials.sort')
                        ->orderBy('id', 'desc');
                }
            ])
            ->get()
            ->pluck('course.arduinos')
            ->map(function ($item) use (&$data) {
                $data = $data->merge($item);
            });

        return $this->response()->baseCollection($data->keyBy('id')->sort(), ArduinoMaterialResource::class);
    }
}
