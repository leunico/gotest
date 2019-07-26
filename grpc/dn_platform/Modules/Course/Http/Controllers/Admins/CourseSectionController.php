<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Course\Http\Requests\StoreCourseSectionPost;
use Modules\Course\Entities\CourseSection;
use function App\responseSuccess;
use Modules\Course\Entities\CourseLesson;
use function App\responseFailed;
use App\Http\Controllers\Controller;

class CourseSectionController extends Controller
{
    /**
     * 获取课程环节列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\CourseSection $section
     * @param int $courseId
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, CourseSection $section, int $courseId = 0)
    {
        $category = $request->input('category', null);
        $status = $request->input('status', null);
        $keyword = $request->input('keyword', null);

        $data = $section->select('id', 'title', 'course_lesson_id', 'category', 'status', 'section_intro', 'section_number')
            ->when($category, function ($query) use ($category) {
                return $query->where('category', $category);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'like', "%$keyword%");
            })
            ->when($courseId, function ($query) use ($courseId) {
                $query->whereIn('course_lesson_id', function ($query) use ($courseId) {
                    $query->from('course_lessons')
                        ->select('id')
                        ->where('course_id', $courseId);
                });
            })
            ->with([
                'courseLesson' => function ($query) {
                    $query->select('id', 'title', 'sort', 'is_code');
                },
            ])
            ->orderBy('section_number')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('courseLesson.title');

        $lessons = empty($courseId) ? [] : CourseLesson::select('id', 'title', 'sort', 'is_code')
            ->OfCourseId($courseId)
            ->orderBy('sort')
            ->get();

        return responseSuccess($data, '', ['lesson_titles' => $lessons]);
    }

    /**
     * 添加课程环节
     *
     * @param \Modules\Course\Http\Requests\StoreCourseSectionPost $request
     * @param \Modules\Course\Entities\CourseSection $section
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreCourseSectionPost $request, CourseSection $section)
    {
        $section->title = $request->title;
        $section->course_lesson_id = $request->course_lesson_id;
        $section->category = $request->category ?? 1; //todo 现阶段没有其它类型
        $section->status = $request->status;
        $section->source_link = $request->input('source_link', '');
        $section->source_duration = $request->input('source_duration', 0);
        $section->section_intro = $request->input('section_intro', '');
        $section->section_number = $request->input('section_number', 0);
        $section->arduino_material_id = $request->input('arduino_material_id', 0);

        $section->getConnection()->transaction(function () use ($section, $request) {
            if ($section->save()) {
                if ($section->isSectionProblem() && ! is_null($request->problems)) {
                    $problems = array_map(function ($item) {
                        return ['quize_time' => $item];
                    }, $request->input('problems', []));

                    $section->problems()->sync($problems);
                }
            }
        });

        return responseSuccess([
            'course_section_id' => $section->id,
        ], '添加课程环节成功');
    }

    /**
     * 获取一条课程环节
     *
     * @param \Modules\Course\Entities\CourseSection $section
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(CourseSection $section)
    {
        $section->load([
            'courseLesson' => function ($query) {
                $query->select('course_lessons.id', 'title', 'sort', 'course_id');
            } ,
            'courseLesson.course' => function ($query) {
                $query->select('courses.id', 'title');
            },
            'arduinoMaterial' => function ($query) {
                $query->select('id', 'name', 'source_link', 'md5', 'is_arduino');
            },
            'problems' => function ($query) {
                $query->select('problems.id', 'category', 'course_category', 'course_section_problem_pivot.quize_time')
                    ->orderBy('course_section_problem_pivot.quize_time');
            },
            'problems.detail' => function ($query) {
                $query->select('problem_details.id', 'problem_text', 'problem_id');
            },
            // 'problems.options' => function ($query) {
            //     $query->select('id', 'option_text', 'problem_id', 'is_ture', 'sort');
            // }
        ]);

        return responseSuccess($section);
    }

    /**
     * 修改课程环节
     *
     * @param \Modules\Course\Http\Requests\StoreCourseSectionPost $request
     * @param \Modules\Course\Entities\CourseSection $section
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreCourseSectionPost $request, CourseSection $section)
    {
        $section->title = $request->title;
        $section->course_lesson_id = $request->course_lesson_id;
        $section->category = $request->category ?? $section->category;
        $section->status = $request->status;
        $section->source_link = $request->input('source_link', '');
        $section->source_duration = $request->input('source_duration', 0);
        $section->section_intro = $request->input('section_intro', '');
        $section->section_number = $request->input('section_number', 0);
        $section->arduino_material_id = $request->input('arduino_material_id', 0);

        $section->getConnection()->transaction(function () use ($section, $request) {
            if ($section->save()) {
                if ($section->isSectionProblem() && ! is_null($request->problems)) {
                    if ($section->category != $request->category) {
                        $section->problems()->sync([]);
                    }
                    $problems = array_map(function ($item) {
                        return ['quize_time' => $item];
                    }, $request->input('problems', []));

                    $section->problems()->sync($problems);
                }
            }
        });

        return responseSuccess([
            'course_section_id' => $section->id,
        ], '修改课程环节成功');
    }

    /**
     * 设置课程环节的排序【拖拽】
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\CourseSection $section
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sort(Request $request, CourseSection $section)
    {
        $sorts = $request->input('sorts', []);
        if (empty($sorts) || !is_array($sorts)) {
            return responseFailed('排序数组参数错误', 422);
        }

        if ($section->batchUpdate($sorts, 'section_number')) {
            return responseSuccess();
        } else {
            return responseFailed('操作失败，请检查', 500);
        }
    }

    /**
     * 设置课程环节的排序【上下】
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\CourseSection $section
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function upDownSort(Request $request, CourseSection $section)
    {
        $this->validate($request, [
            'sort' => 'required|numeric|max:2147483647',
        ]);

        $section->section_number = $request->input('sort', 0);
        if ($section->save()) {
            return responseSuccess();
        } else {
            return responseFailed('操作失败，请检查', 500);
        }
    }

    /**
     * 上下架一条课程章节
     *
     * @param \Modules\Course\Entities\CourseSection $section
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(CourseSection $section)
    {
        $section->actionStatus();

        return responseSuccess();
    }
}
