<?php

declare(strict_types=1);

namespace Modules\Course\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Course\Entities\Course;
use Modules\Course\Entities\MusicTheory;
use Modules\Course\Transformers\CourseResource;
use Illuminate\Http\JsonResponse;

class MusicTheoryController extends Controller
{
    /**
     * 乐理包上课页面
     *
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(Course $course): JsonResponse
    {
        $course->load([
            'musicTheories' => function ($query) {
                $query->where('status', MusicTheory::STATUS_NO)
                    ->orderBy('id');
            },
            // 'musicTheories.musicLearnProgresses' => function ($query) { // todo 上下两种其实都可以
            //     $query->select('id', 'user_id', 'music_id')
            //         ->where('user_id', request()->user()->id);
            // },
            'musicTheories.musicLearnRecords' => function ($query) {
                $query->select('id', 'end_at', 'music_id', 'duration')
                    ->where('user_id', request()->user()->id);
            }
        ]);

        return $this->response()->item($course, CourseResource::class);
    }
}
