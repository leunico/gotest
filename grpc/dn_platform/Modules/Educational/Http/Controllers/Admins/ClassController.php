<?php

namespace Modules\Educational\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Educational\Http\Requests\StoreClassPost;
use Modules\Educational\Entities\StudyClass;
use function App\responseSuccess;
use function App\responseFailed;
use Illuminate\Support\Carbon;
use Modules\Educational\Entities\ClassStudent;
use App\Rules\ArrayExists;
use Modules\Course\Entities\CourseLesson;
use Illuminate\Validation\Rule;
use Modules\Personal\Entities\CourseUser;
use function App\arrayKeyLast;
use Modules\Educational\Http\Controllers\Concerns\ControllerExtend;
use App\User;
use App\Rules\ArrayUnique;
use Modules\Personal\Entities\LearnRecord;

class ClassController extends Controller
{
    use ControllerExtend;

    /**
     * 班级列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\StudyClass $class
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function index(Request $request, StudyClass $class)
    {
        $perPage = $request->input('per_page', 15);

        $startTime = $request->input('start_time', 0);
        $endTime = $request->input('end_time', 0);
        $unlockStartTime = $request->input('unlock_start_time', 0);
        $unlockEndTime = $request->input('unlock_end_time', 0);
        $teacher = $request->input('teacher_id', null);
        $pattern = $request->input('pattern', null);
        $status = $request->input('status', null);
        $isStart = $request->input('is_start', null);
        $keyword = $request->input('keyword', null);
        $courseCategory = $request->input('course_category', null);
        $category = $request->input('category', null);
        $course = $request->input('course_id', null);
        $bigCourse = $request->input('big_course_id', null);

        $data = $class->select('id', 'name', 'teacher_id', 'pattern', 'frequency', 'unlocak_times', 'entry_at', 'unlock_at', 'status', 'created_at', 'course_category', 'category', 'course_id', 'big_course_id')
            ->when($teacher, function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher);
            })
            ->when($pattern, function ($query) use ($pattern) {
                $query->where('pattern', $pattern);
            })
            ->when($courseCategory, function ($query) use ($courseCategory) {
                $query->where('course_category', $courseCategory);
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($course, function ($query) use ($course) {
                $query->where('course_id', $course);
            })
            ->when($bigCourse, function ($query) use ($bigCourse) {
                $query->where('big_course_id', $bigCourse);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when(! is_null($isStart), function ($query) use ($isStart) {
                $query->where('entry_at', (empty($isStart) ? '<' : '>='), Carbon::now());
            })
            ->when((! empty($startTime) || ! empty($endTime)), function ($query) use ($startTime, $endTime) {
                $query->whereBetween('created_at', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfDay()]);
            })
            ->when((! empty($unlockStartTime) || ! empty($unlockEndTime)), function ($query) use ($unlockStartTime, $unlockEndTime) {
                $query->whereBetween('unlock_at', [$unlockStartTime, (empty($unlockEndTime) ? Carbon::now() : Carbon::parse($unlockEndTime))->endOfDay()]);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%$keyword%");
            })
            ->with([
                'teacher',
                'course' => function ($query) {
                    return $query->select('id', 'title', 'type');
                },
                'bigCourse' => function ($query) {
                    return $query->select('id', 'title');
                },
                'courses' => function ($query) {
                    return $query->select('courses.id', 'courses.title', 'courses.type');
                },
                'courses.lessons' => function ($query) {
                    return $query->select('course_lessons.id', 'course_lessons.course_id');
                },
                'courses.lessons.sections' => function ($query) {
                    return $query->select('course_sections.id', 'course_sections.course_lesson_id');
                },
                'students' => function ($query) {
                    return $query->select('users.id', 'real_name');
                },
                // 'students.learnRecords' => function ($query) {
                //     $query->select('learn_records.user_id', 'section_id', 'duration', 'entry_at');
                // }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        collect($data->items())->map(function ($item) {
            $item->count_student = $item->students->count();
            $item->count_learning = LearnRecord::whereIn('user_id', $item->students->pluck('id'))
                ->whereIn('section_id', $item->courses->pluck('lessons')->flatten(1)->pluck('sections')->flatten(1)->pluck('id'))
                ->get()
                ->sum('duration');
            // $count_learning = 0;
            // if ($item->students->isNotEmpty()) {
            //     $item->students->map(function ($val) use ($item, &$count_learning) {
            //         $count_learning = $val->learnRecords->isNotEmpty() ?
            //             ($count_learning + $val->learnRecords->whereIn('section_id', $item->courses->pluck('lessons')->flatten(1)->pluck('sections')->flatten(1)->pluck('id'))->sum('duration')) :
            //             $count_learning;
            //     });
            // }
            // $item->count_learning = 0;
            // unset($item->courses, $item->students);
        });

        return responseSuccess($data, 'Success', [
            'categoryMap' => StudyClass::$categorys,
            'patternMap' => StudyClass::$patterns,
            'frequencyMap' => StudyClass::$frequencies
        ]);
    }

    /**
     * 添加班级
     *
     * @param \Modules\Educational\Entities\StudyClass $class
     * @param \Modules\Educational\Http\Requests\StoreClassPost $request
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function store(StoreClassPost $request, StudyClass $class)
    {
        $class->name = $request->name;
        $class->entry_at = $request->entry_at;
        $class->category = $request->category;
        $class->pattern = $request->pattern;
        $class->frequency = $request->frequency;
        $class->unlock_at = $request->unlock_at;
        $class->unlocak_times = $request->unlocak_times;
        $class->course_category = $request->course_category;
        $class->teacher_id = $request->input('teacher_id', 0);
        $class->status = $request->status;
        $class->leave_at = $request->input('leave_at');
        $class->big_course_id = $request->input('big_course_id', 0);
        $class->course_id = $request->input('course_id', 0);

        $class->getConnection()->transaction(function () use ($class) {
            if ($class->save()) {
                $class->courses()->attach($class->isCategoryBigCourse() ? $class->bigCoursePivots->pluck('course_id') : [$class->course_id]);
            }
        });

        return responseSuccess([
            'class_id' => $class->id
        ], '添加班级成功');
    }

    /**
     * 获取班级.
     *
     * @param \Modules\Educational\Entities\StudyClass $class
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function edit(StudyClass $class)
    {
        $class->load([
            'course' => function ($query) {
                return $query->select('id', 'title', 'type');
            },
            'bigCourse' => function ($query) {
                return $query->select('id', 'title');
            },
            'classCourseLesson' => function ($query) {
                return $query->orderBy('unlock_day', 'desc')
                    ->take(1);
            },
            'teacher'
        ]);

        return responseSuccess($class);
    }

    /**
     * 设置课程.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\StudyClass $class
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function addCourseLessons(Request $request, StudyClass $class)
    {
        $this->validate($request, [
            'unlocaks' => [
                'array',
                new ArrayExists(CourseLesson::whereIn('course_id', $class->coursePovits->pluck('course_id')), true)
            ]
        ]);

        $class->courseLessons()->sync($request->unlocaks);

        return responseSuccess();
    }

    /**
     * 主题解锁上下移动.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\StudyClass $class
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function moveCourseLessons(Request $request, StudyClass $class)
    {
        $this->validate($request, [
            'sort' => 'required|in:0,1',
            'date' => 'required|date',
            'prev_date' => 'required_if:sort,0|date',
            'end_date' => 'required_if:sort,1|date',
        ]);

        if (! $class->isFrequencyWeek() && ! $class->isFrequencyMonth()) {
            return responseFailed('不支持的类型', 422);
        }

        $class->load([
            'classCourseLessons' => function ($query) {
                return $query->select('course_lesson_id', 'class_id', 'unlock_day', 'course_id')
                    ->orderBy('unlock_day');
            }
        ]);

        $data = collect();
        $unlocks = $class->classCourseLessons->groupBy('unlock_day')->toArray();
        $supplement = empty($request->sort) ? $request->prev_date : $request->end_date;
        foreach ($unlocks as $key => $value) {
            if ($request->date > $key) {
                $data = $data->merge($value);
                unset($unlocks[$key]);
            }
        }

        $data = $this->recursionUnlock(empty($request->sort) ? array_reverse($unlocks) : $unlocks, $data, $supplement)
            ->keyBy('course_lesson_id');
        $class->courseLessons()->sync($data);

        return responseSuccess();
    }

    /**
     * 获取班级课程.
     *
     * @param \Modules\Educational\Entities\StudyClass $class
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function courses(StudyClass $class)
    {
        $class->load([
            'courses' => function ($query) {
                return $query->select('courses.id', 'courses.title', 'courses.type');
            },
            'courses.lessons' => function ($query) {
                return $query->select('course_lessons.id', 'course_lessons.title', 'course_lessons.course_id');
            },
            'courseLessons' => function ($query) {
                return $query->orderBy('class_course_lesson_unlocaks.unlock_day');
            }
        ]);

        return responseSuccess($class, 'Success.', ['unlockDayMap' => $class->unlock_lists]);
    }

    /**
     * 学员列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\ClassStudent $classStudent
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function students(Request $request, CourseUser $courseUser)
    {
        $perPage = $request->input('per_page', 15);

        $keyword = $request->input('keyword', null);
        // $status = $request->input('status', null);
        // $classStatus = $request->input('class_status', null);
        // $entryStartTime = $request->input('entry_start_time', 0);
        // $entryEndTime = $request->input('entry_end_time', 0);
        // $leaveStartTime = $request->input('leave_start_time', 0);
        // $leaveEndTime = $request->input('leave_end_time', 0);

        $data = CourseUser::select('course_users.id', 'user_id', 'course_id')
            ->where('course_users.status', CourseUser::STATUS_NO)
            ->when($keyword, function ($query) use ($keyword) {
                return $query->whereIn('course_users.user_id', function ($query) use ($keyword) {
                    return $query->from('users')
                        ->select('id')
                        ->where('real_name', 'LIKE', "%$keyword%")
                        ->orWhere('users.name', 'LIKE', "%$keyword%")
                        ->orWhere('phone', 'LIKE', "%$keyword%");
                });
            })
            ->with([
                'user' => function ($query) {
                    return $query->select('name', 'real_name', 'users.id', 'phone', 'grade', 'sex');
                },
                'user.allClass' => function ($query) {
                    return $query->select('classes.id', 'classes.name', 'classes.status', 'entry_at', 'leave_at', 'classes.category', 'classes.course_id', 'big_course_id');
                },
                'user.allClass.bigCoursePivots' => function ($query) {
                    return $query->select('big_course_course_pivot.course_id', 'big_course_course_pivot.id', 'big_course_course_pivot.big_course_id');
                }
            ])
            ->paginate($perPage);

        collect($data->items())->map(function ($item) {
            $item['name'] = $item->user->name;
            $item['real_name'] = $item->user->real_name;
            $item['phone'] = $item->user->phone;
            $item['grade'] = $item->user->grade;
            $item['sex'] = $item->user->sex;
            $item['class_id'] = $item['class_name'] = $item['class_status'] = $item['entry_at'] = $item['leave_at'] = null;
            if ($item->user->allClass->isNotEmpty()) {
                $item->user->allClass->each(function ($val) use ($item) {
                    if ($val->course_id == $item->course_id || ($val->bigCoursePivots && $val->bigCoursePivots->where('course_id', $item->course_id)->isNotEmpty())) {
                        $item['class_id'] = $val->id;
                        $item['class_name'] = $val->name;
                        $item['class_status'] = $val->status;
                        $item['entry_at'] = $val->entry_at;
                        $item['leave_at'] = $val->leave_at;
                    }
                });
            }
            if (empty($item['class_id'])) {
                $item['status'] = 0;
            } elseif ($item['entry_at'] && Carbon::now()->lt(Carbon::parse($item['entry_at']))) {
                $item['status'] = 2;
            } elseif ($item['leave_at'] && Carbon::now()->gt(Carbon::parse($item['leave_at']))) {
                $item['status'] = 3;
            } elseif ($item['entry_at'] && Carbon::now()->gte(Carbon::parse($item['entry_at']) && Carbon::now()->lte(Carbon::parse($item['leave_at'])))) {
                $item['status'] = 1;
            } else {
                $item['status'] = -1;
            }
            unset($item['user']);
        });

        return responseSuccess($data);
    }

    /**
     * 班级学员列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\StudyClass $class
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function classStudent(Request $request, StudyClass $class)
    {
        $keyword = $request->input('keyword', null);

        $class->load([
            'students' => function ($query) use ($keyword) {
                return $query->select('users.id', 'name', 'real_name', 'phone', 'grade')
                    ->when($keyword, function ($query) use ($keyword) {
                        return $query->where('real_name', 'Like', "%$keyword%")
                            ->orWhere('phone', 'Like', "%$keyword%");
                    });
            },
            'students.courseUsers'
        ]);

        $class->students->map(function ($item) use ($class) {
            $item->order_time = $item->courseUsers->keyBy('class_id')->get($class->id);
        });

        return responseSuccess($class);
    }

    /**
     * 添加学员.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\StudyClass $class
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function addStudent(Request $request, StudyClass $class)
    {
        $this->validate($request, ['user_id' => [
            'required',
            'array',
            new ArrayExists(new User),
            new ArrayUnique(ClassStudent::where('class_id', $class->id)), // todo 非必需！
            function ($attribute, $value, $fail) use ($class) {
                $classCourses = $class->coursePovits->pluck('course_id');
                foreach ($value as $id) {
                    if (CourseUser::where('user_id', $id)
                        ->whereIn('course_id', $classCourses)
                        ->where('class_id', 0)
                        ->get()
                        ->isEmpty()) {
                        return $fail("用户[" . User::find($id)->real_name . "]的系列课程（" . $classCourses->implode('，') . "）已经有班级了！");
                    }
                }
            },
        ]]);

        $class->getConnection()->transaction(function () use ($class, $request) {
            $class->students()->attach($request->user_id);
            CourseUser::whereIn('user_id', $request->user_id)
                ->whereIn('course_id', $class->coursePovits->pluck('course_id'))
                ->update(['class_id' => $class->id]);
        });

        return responseSuccess();
    }

    /**
     * 移出班级.
     *
     * @param \Modules\Educational\Entities\ClassStudent $classStudent
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function delStudent(ClassStudent $classStudent)
    {
        $classStudent->delete();

        return responseSuccess();
    }

    /**
     * 修改班级
     *
     * @param \Modules\Educational\Entities\StudyClass $class
     * @param \Modules\Educational\Http\Requests\StoreClassPost $request
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function update(StoreClassPost $request, StudyClass $class)
    {
        $class->name = $request->name;
        $class->entry_at = $request->entry_at;
        $class->category = $request->category;
        $class->pattern = $request->pattern;
        $class->frequency = $request->frequency;
        $class->unlock_at = $request->unlock_at;
        $class->unlocak_times = $request->unlocak_times;
        $class->course_category = $request->course_category;
        $class->teacher_id = $request->input('teacher_id', 0);
        $class->status = $request->status;
        $class->leave_at = $request->input('leave_at');
        $class->big_course_id = $request->input('big_course_id', 0);
        $class->course_id = $request->input('course_id', 0);

        $class->getConnection()->transaction(function () use ($class) {
            if ($class->save()) {
                $class->courses()->sync($class->isCategoryBigCourse() ? $class->bigCoursePivots->pluck('course_id') : [$class->course_id]);
            }
        });

        return responseSuccess([
            'class_id' => $class->id
        ], '修改班级成功');
    }

    /**
     * 发布|取消
     *
     * @param \Modules\Educational\Entities\StudyClass $class
     * @return \Illuminate\Http\Response
     * @author lizx
     */
    public function action(StudyClass $class)
    {
        if ($class->classStudents->isNotEmpty() && ! empty($class->status)) {
            return responseFailed('有学员的班级不可取消！', 422);
        }

        $class->actionStatus();

        return responseSuccess();
    }
}
