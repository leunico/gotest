<?php
/**
 * Created by PhpStorm.
 * User: MRW
 * Date: 2018/11/8
 * Time: 11:46
 */

namespace Modules\Personal\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Modules\Course\Entities\Problem;
use Modules\Course\Entities\CourseLesson;
use Modules\Course\Entities\CourseSection;
use Illuminate\Support\Facades\Auth;
use Modules\Personal\Entities\SubjectSubmission;
use Modules\Personal\Http\Requests\StudyRecordRequest;
use Modules\Personal\Transformers\StudyRecordResource;
use function App\removeNullElement;
use Modules\Personal\Entities\CollectLearnRecord;
use Illuminate\Http\Request;

class StudyRecordController extends Controller
{
    //学习报告
    public function studyReport(Request $request,CourseLesson $lesson)
    {
        $user_id = $request->input('user_id');
        $res = CollectLearnRecord::where([
            'user_id' => $user_id,
            'course_lesson_id' => $lesson->id
        ])->first();
        if(empty($res) || $res->status != 1){
            return $this->response()->errorNotFound('该主题还没学习完或者用户没有购买该课程！');
        }
        $data = $lesson->load([
            'works' => function ($query) use($user_id){
                $query->where('user_id', $user_id);
            },
            'sections' => function ($query) {
                $query->where('status', CourseSection::SECTION_STATUS_ON)
                    ->select('id', 'title', 'course_lesson_id', 'category', 'source_duration', 'status', 'section_number')
                    ->orderBy('id', 'asc');
            },
            'sections.records' => function ($query) use($user_id){
                $query->where('user_id', $user_id)
                    ->select('id', 'user_id', 'section_id', 'duration');
            },
            'sections.subjects' => function ($query) use($user_id){
                $query->where('user_id', $user_id);
            },
            'sections.sectionPivots',
            'sections.sectionPivots.detail' => function ($query) {
                $query->select('id', 'problem_id', 'problem_text');
            },
            'sections.sectionPivots.problem' => function ($query) {
                $query->select('id', 'category');
            },
        ]);

        foreach ($data->works as $vo) {
            $vo->file_url = $vo->json_url;
            $vo->image_cover = $vo->image_url;
        }
        $data->total_subject = 0;
        $data->total_correct_number = 0;
        $data->total_error_number = 0;
        foreach ($data->sections as $vo) {
            //学习记录
            $record_arr = array_column($vo->records->toarray(), 'duration');
            $vo->learn_time = ceil(array_sum($record_arr) / 1000);

            //答题情况
            $tmp = [];
            foreach ($vo->subjects as $value) {
                $tmp[$value->problem_id][$value->is_correct][] = $value->toarray();
            }
            //记录
            foreach ($vo->sectionPivots as $value) {
                $value->correct_number = 0;
                $value->error_number = 0;
                if (!empty($tmp[$value->problem_id][1])) {
                    $value->correct_number = count($tmp[$value->problem_id][1]);
                }
                if (!empty($tmp[$value->problem_id][0])) {
                    $value->error_number = count($tmp[$value->problem_id][0]);
                }
                $value->subject_total_number = $value->correct_number + $value->error_number;
                $data->total_subject++;
                if(in_array($value->problem->category, Problem::$choice_question_category)){
                    $data->total_correct_number = $data->total_correct_number + $value->correct_number;
                    $data->total_error_number = $data->total_error_number + $value->error_number;
                }
            }
            $data->total_submit_number = $data->total_correct_number + $data->total_error_number;
        }
        return $this->response()->item($data, StudyRecordResource::class);
    }
}