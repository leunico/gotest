<?php
/**
 * Created by PhpStorm.
 * User: MRW
 * Date: 2018/11/5
 * Time: 12:05
 */

namespace Modules\Personal\Http\Controllers\Apis;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Modules\Course\Entities\CourseLesson;
use Modules\Course\Entities\CourseSection;
use Modules\Course\Entities\MusicTheory;
use Modules\Course\Entities\CourseMusicTheoryPivot;
use Modules\Personal\Entities\LearnRecord;
use Modules\Personal\Entities\CollectLearnRecord;
use Modules\Personal\Entities\LearnProgress;
use Modules\Personal\Entities\MusicCollectLearnRecord;
use Modules\Personal\Entities\MusicLearnProgress;
use Carbon\Carbon;
use Modules\Personal\Http\Requests\LearnRecordRequest;
use Modules\Personal\Http\Requests\WatchRecordRequest;

class AttendClassController extends Controller
{
    public function learnRecord(LearnRecordRequest $request)
    {
        $form_data = $request->only(['id', 'type', 'course_id']);
        $data = [];
        if ($form_data['type'] == 1) {
            $data = $this->sectionLearnRecord($form_data);
        } elseif ($form_data['type'] == 2) {
            $data = $this->musicLearnRecord($form_data);
        }
        return $this->response()->success($data);
    }

    private function sectionLearnRecord($form_data)
    {
        $section = CourseSection::findOrFail($form_data['id']);
        $progresse = LearnProgress::where([
            'user_id' => Auth::id(),
            'section_id' => $section->id
        ])->first();
        if (empty($progresse)) {
            //主题系列
            $course_id = CourseLesson::where('id', $section->course_lesson_id)->value('course_id');
            $collect = CollectLearnRecord::where([
                'user_id' => Auth::id(),
                'course_id' => $course_id,
                'course_lesson_id' => $section->course_lesson_id,
            ])->first();
            if (empty($collect)) {
                $collect = CollectLearnRecord::create([
                    'user_id' => Auth::id(),
                    'course_id' => $course_id,
                    'course_lesson_id' => $section->course_lesson_id,
                ]);
            }
            //生成记录
            LearnProgress::create([
                'user_id' => Auth::id(),
                'collect_learn_record_id' => $collect->id,
                'section_id' => $section->id
            ]);
            //主题下所有的
            $lesson_sections = CourseSection::where([
                'course_lesson_id' => $section->course_lesson_id,
                'status' => CourseSection::SECTION_STATUS_ON,
            ])->get(['id']);
            //所有观看记录
            $learn_progresses = LearnProgress::where([
                'user_id' => Auth::id(),
                'collect_learn_record_id' => $collect->id
            ])->get();
            $arr_learn_progresses = array_column($learn_progresses->toarray(), 'section_id');
            $status = 1;
            foreach ($lesson_sections as $lesson_section) {
                if (!in_array($lesson_section->id, $arr_learn_progresses)) {
                    $status = 0;
                    break;
                }
            }
            if ($status == 1) {
                $collect->status = 1;
                $collect->save();
            }
        }
        $learn_record = LearnRecord::where([
            'user_id' => Auth::id(),
            'section_id' => $section->id
        ])->orderBy('id', 'Desc')->first(['end_at']);
        $data['start_at'] = 0;
        if (!empty($learn_record['end_at'])) {
            if (($section->source_duration * 1000 - $learn_record['end_at']) >= 500) {
                $data['start_at'] = $learn_record['end_at'];
            }
        }
        return $data;
    }

    private function musicLearnRecord($form_data)
    {
        $music = MusicTheory::findOrFail($form_data['id']);
        $progresse = MusicLearnProgress::where([
            'user_id' => Auth::id(),
            'music_id' => $music->id
        ])->first();
        if (empty($progresse)) {
            //生成记录
            MusicLearnProgress::create([
                'user_id' => Auth::id(),
                'music_id' => $music->id
            ]);
            //课程下所有的乐理
            $course_musics = CourseMusicTheoryPivot::where('course_id', $form_data['course_id'])->get();
            //所有观看记录
            $learn_progresses = MusicLearnProgress::where([
                'user_id' => Auth::id(),
            ])->get();
            $arr_learn_progresses = array_column($learn_progresses->toarray(), 'music_id');
            $status = 1;
            foreach ($course_musics as $course_music) {
                if (!in_array($course_music->music_theory_id, $arr_learn_progresses)) {
                    $status = 0;
                    break;
                }
            }
            MusicCollectLearnRecord::updateOrCreate(
                ['user_id' => Auth::id(), 'course_id' => $form_data['course_id']],
                ['status' => $status]
            );
        }
        $learn_record = LearnRecord::where([
            'user_id' => Auth::id(),
            'music_id' => $music->id
        ])->orderBy('id', 'Desc')->first(['end_at']);
        $data['start_at'] = 0;
        if (!empty($learn_record['end_at'])) {
            if (($music->source_duration * 1000 - $learn_record['end_at']) > 500) {
                $data['start_at'] = $learn_record['end_at'];
            }
        }
        return $data;
    }

    public function watchRecord(WatchRecordRequest $request)
    {
        $user = Auth::user();
        $formData = $request->only(['start_at', 'end_at', 'id', 'type']);
        if ($formData['type'] == 1) {
            $field = 'section_id';
            $durations = CourseSection::findOrFail($formData['id']);
        } else {
            $field = 'music_id';
            $durations = MusicTheory::findOrFail($formData['id']);
        }
        $duration = $durations->source_duration;

        $start_at = floor($formData['start_at']);
        $end_at = floor($formData['end_at']);

        if ($start_at > $end_at || $end_at > ($duration * 1000 + 2000)) {
            return $this->response()->success('无操作');
        }
        $diff = $end_at - $start_at;
        $leave_at = Carbon::now();
        $second = ceil($diff / 1000);
        $entryAt = $leave_at->copy()->subSeconds($second);
        LearnRecord::create([
            'user_id' => $user->id,
            $field => $formData['id'],
            'entry_at' => $entryAt->toDateTimeString(),
            'leave_at' => $leave_at->toDateTimeString(),
            'start_at' => floor($formData['start_at']),
            'end_at' => floor($formData['end_at']),
            'duration' => $diff,
        ]);
        return $this->response()->success('成功');
    }

}
