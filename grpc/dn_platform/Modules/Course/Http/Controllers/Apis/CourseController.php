<?php

declare(strict_types=1);

namespace Modules\Course\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Course\Entities\Course;
use Modules\Personal\Entities\CourseUser;
use Modules\Course\Entities\CourseLesson;
use Modules\Course\Entities\CourseSection;
use Modules\Course\Transformers\CourseResource;
use Illuminate\Http\JsonResponse;
use Modules\Personal\Entities\MusicCollectLearnRecord;
use Modules\Course\Entities\BigCourse;
use Modules\Personal\Entities\CollectLearnRecord;
use Illuminate\Support\Carbon;

class CourseController extends Controller
{
    /**
     * Show the course user.
     *
     * @param int $category
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function list(int $category = 1): JsonResponse
    {
        $user = $this->user();
        $isPermission = $user->isSuperAdmin() || $user->can('api-course[' . $category . ']');
        $courses = Course::select('courses.id', 'title', 'level', 'type')
            ->with([
                'bigCourses' => function ($query) {
                    $query->where('status', BigCourse::STATUS_ON)
                        ->select('big_courses.id', 'big_courses.title', 'big_course_course_pivot.sort');
                }
            ])
            ->when(! $isPermission, function ($query) use ($user, $category) {
                return $query->rightjoin('course_users', 'course_users.course_id', 'courses.id')
                    ->select('courses.id', 'title', 'level', 'type')
                    ->where('course_users.user_id', $user->id)
                    ->where('course_users.status', CourseUser::STATUS_NO)
                    ->when($user->introduce && $user->userCourseLessons->isNotEmpty(), function ($query) use ($user, $category) {
                        return $query->union(Course::select('courses.id', 'title', 'level', 'type')
                            ->whereIn('id', $user->userCourseLessons->pluck('course_id')->unique())
                            ->where('category', $category));
                    });
            })
            ->where('category', $category)
            // ->where('courses.status', Course::STATUS_NO) // todo 下架也要显示
            ->orderBy('level')
            // ->orderBy('courses.id', 'asc')
            ->get();

        return $this->response()->collection($courses, CourseResource::class);
    }

    /**
     * Show the course.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(Request $request, Course $course): JsonResponse
    {
        /**
         * todo 乐理包部分[产品暂时不要]
         *
         'musicCollectLearnRecord' => function ($query) use ($user) {
             $query->where('user_id', $user->id)
                 ->where('status', MusicCollectLearnRecord::STATUS_ON) // todo 是否要学完才算？
                 ->select('id', 'status', 'course_id', 'user_id');
         },
         */
        $user = $this->user();
        $course->class = $user->getClass((int) $course->id);
        $course->load([
            'lessons' => function ($query) use ($user, $course) {
                $query->select('course_lessons.id', 'title', 'cover_id', 'course_lessons.course_id', 'count_user_learns', 'is_code', 'is_drainage')
                    ->withCount(['learnRecords'])
                    ->where('status', CourseLesson::LESSON_STATUS_ON)
                    ->orderBy('sort')
                    ->when(! $user->courseUser && $user->introduce && ! $user->isSuperAdmin() && ! $user->can('api-course[' . $course->category . ']'),
                    function ($query) use ($user) {
                        return $query->rightjoin('user_course_lessons', 'course_lessons.id', 'user_course_lessons.course_lesson_id')
                            ->where('user_id', $user->id);
                    });
            },
            'lessons.cover',
            'lessons.sections' => function ($query) {
                $query->select('id', 'course_lesson_id', 'section_number')
                    ->where('status', CourseSection::SECTION_STATUS_ON);
            },
            'lessons.works' => function ($query) use ($user) {
                $query->select('id', 'title', 'file_url', 'lesson_id', 'type', 'share')
                    ->where('user_id', $user->id);
            },
            'lessons.userLearnRecord' => function ($query) use ($user) {
                $query->select('id', 'status', 'course_id', 'course_lesson_id')
                    ->where('user_id', $user->id);
            },
            'lessons.userLearnRecord.learnProgresses' => function ($query) use ($user) {
                $query->select('id', 'section_id', 'collect_learn_record_id');
            },
            'lessons.unlockDays' => function ($query) use ($course) {
                return $query->select('class_course_lesson_unlocaks.id', 'course_lesson_id', 'unlock_day')
                    ->where('class_id', $course->class ? $course->class->id : null);
            }
        ]);

        // todo 解锁规则
        $course->lessons->map(function ($item) use ($user, $course) {
            if ($user->isSuperAdmin() ||
                $user->can('api-course[' . $course->category . ']') ||
                $user->can('course-unlock-no-limit') ||
                (! $user->courseUser && $user->introduce) ||
                $course->isNotDrainage() ||
                $item->isNotDrainage()) {
                $item->is_unlock = true;
            } else {
                $item->is_unlock = $item->unlockDays ? Carbon::now()->gte(Carbon::parse($item->unlockDays->unlock_day)) : false;
            }
        });

        return $this->response()->item($course, CourseResource::class);
    }

    /**
     * Show the course of wechat.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $category
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function wechatIndex(Request $request, int $category = 1): JsonResponse
    {
        $user = $request->user();
        $ids = $request->input('ids', null);
        $isDrainage = $request->input('is_drainage', null);

        $data = Course::select('id', 'title', 'course_intro', 'price', 'original_price', 'cover_id', 'level', 'category', 'status', 'is_mail')
            ->when($ids && str_contains($ids, ','), function ($query) use ($ids) {
                $query->whereIn('id', explode(',', $ids));
            })
            ->when($user, function ($query) use ($user) {
                $query->with(['courseUsers' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('status', CourseUser::STATUS_NO)
                        ->select('id', 'course_id');
                }]);
            })
            ->when(! is_null($isDrainage), function ($query) use ($isDrainage) {
                $query->where('is_drainage', $isDrainage);
            })
            ->with(['cover'])
            ->where('category', $category)
            ->where('status', Course::STATUS_NO)
            ->orderBy('level')
            ->orderBy('id', 'desc')
            ->get();

        return $this->response()->collection($data, CourseResource::class);
    }

    /**
     * Show the course of wechat.
     *
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function wechatShow(Course $course): JsonResponse
    {
        $course->load([
            'cover',
            'lessons' => function ($query) {
                $query->select('id', 'title', 'cover_id', 'course_id', 'count_user_learns', 'is_code', 'lesson_intro', 'knowledge')
                    ->where('status', CourseLesson::LESSON_STATUS_ON);
            },
            'lessons.cover'
        ]);

        return $this->response()->item($course, CourseResource::class);
    }
}
