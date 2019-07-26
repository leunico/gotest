<?php

namespace Modules\Course\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\FileResource;
use Illuminate\Support\Carbon;
use Modules\Course\Entities\Course;
use Modules\Course\Entities\BigCourse;
use Illuminate\Http\Resources\PotentiallyMissing;
use Modules\Educational\Transformers\StudyClassResource;

class CourseResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if (! ($loadBigCourse = $this->whenLoaded('bigCourses') instanceof PotentiallyMissing) && $this->whenLoaded('bigCourses')->isEmpty()) {
            $this->bigCourses = collect([new BigCourse([
                'id' => - ((int) $this->type),
                'title' => isset(Course::$courseTypeMap[$this->type]) ? Course::$courseTypeMap[$this->type] : '其它',
                'sort' => $this->id,
            ])]);
        }

        /**
         * todo 是否学完课程
         *
         return $this->lessons && $this->collectLearnRecords ?
            $this->lessons->pluck('id')->diff($this->collectLearnRecords->pluck('course_lesson_id'))->isEmpty() : false;
         */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'level' => $this->level,
            'price' => (float) $this->price / 100,
            'original_price' => (float) $this->original_price / 100,
            'course_intro' => $this->course_intro,
            'category' => $this->category,
            'is_mail' => $this->is_mail,
            'is_drainage' => $this->is_drainage,
            'class' => $this->class ? new StudyClassResource($this->class) : null,
            'cover' => new FileResource($this->whenLoaded('cover')),
            'big_courses' => $this->when(! $loadBigCourse, BigCourseResource::collection($this->bigCourses)),
            'is_buy' => $this->when($request->routeIs('course-wechat-list'), function () use ($request) {
                return $request->user() && $this->courseUsers ? $this->courseUsers->isNotEmpty() : false;
            }),
            'music_theories' => $this->when($request->routeIs('music-theory-show'), function () {
                return MusicTheoryResource::collection($this->musicTheories); // todo 这里其实可以直接用whenLoaded的，方便扩展才写成闭包
            }),
            'old_music_theories' => $this->when($request->routeIs('music-theory-show'), function () use ($request) {
                return MusicTheoryResource::collection($request->user()->musicTheories);
            }),
            'is_learn_music_theory' => true, // todo 暂时不要这个了
            'lessons' => $request->routeIs('course-show') ? CourseLessonCourseResource::collection($this->lessons) :
                ($request->routeIs('course-wechat-show') ? CourseLessonWechatResource::collection($this->lessons) : null),
        ];
    }
}
