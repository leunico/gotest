<?php

namespace Modules\Personal\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\User;
use Modules\Personal\Http\Requests\HomeworksRequest;
use Modules\Personal\Http\Requests\WorksRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Modules\Personal\Entities\Work;
use function App\tempdir;
use function App\uniqueName;
use function App\removeNullElement;
use Modules\Course\Entities\CourseLesson;
use Modules\Personal\Entities\ThumbsUp;
use Illuminate\Http\Request;
use Modules\Personal\Transformers\WorkResource;
use Modules\Personal\Entities\CourseUser;
use Modules\Personal\Transformers\LessonWorkListResource;
use Modules\Personal\Http\Requests\UpdateWorkRequest;
use Illuminate\Support\Facades\DB;
use Modules\Personal\Entities\WorkSbfile;

class WorkController extends Controller
{
    /**
     * 提交作业
     */
    public function submit(HomeworksRequest $request)
    {
        $user = Auth::user();
        $formData = $request->only(['file_url', 'type', 'description', 'lesson_id', 'image_cover']);
        $formData = removeNullElement($formData);
        $formData['user_id'] = $user->id;
        $title = CourseLesson::where('id', $formData['lesson_id'])->value('title');
        $count = Work::where([
            'user_id' => $user->id,
            'lesson_id' => $formData['lesson_id']
        ])->count();
        $formData['title'] = $title . '_' . ($count + 1);
        $work_data = Work::create($formData);
        return $this->response()->success($work_data);
    }

    /**
     * scratch_arduino
     */
    public function saveWork(WorksRequest $request)
    {
        $user = Auth::user();
        $formData = $request->only(['title', 'lesson_id', 'description', 'image_cover', 'file', 'board_type']);
        $formData = removeNullElement($formData);
        //作品信息
        $formData['user_id'] = $user->id;
        $formData['file_url'] = $this->handle_zip($formData['file']);
        $formData['image_cover'] = $this->uploadFile($request->file('image_cover'));
        $formData['type'] = 'scratch_arduino';
        unset($formData['file']);
        $work_data = Work::create($formData);
        return $this->response()->success($work_data);
    }

    /**
     * 更新scratch_arduino
     */
    public function updateWork(Work $work, UpdateWorkRequest $request)
    {
        $user = Auth::user();
        if ($user->id != $work->user_id) {
            return $this->response()->errorForbidden('不能修改别人的作品');
        }
        $formData = $request->only(['title', 'lesson_id', 'description', 'image_cover', 'file', 'board_type']);
        $formData = removeNullElement($formData);
        //作品信息
        $formData['user_id'] = $user->id;
        $formData['file_url'] = $this->handle_zip($formData['file']);
        if (!empty($formData['image_cover'])) {
            $formData['image_cover'] = $this->uploadFile($request->file('image_cover'));
        }
        unset($formData['file']);
        $work->update($formData);
        return $this->response()->success($work);
    }

    /**
     * 单个作品详情
     */
    public function workDetail(Work $work)
    {
        $work->user = User::find($work->user_id, ['id', 'name', 'real_name']);
        $work->file_url = $work->json_url;
        $work->image_cover = $work->image_url;
        if($work->type == 'scratch_arduino'){
            $work->sbfile = WorkSbfile::where('work_id',$work->id)
                ->orderBy('id','DESC')
                ->first();
        }
        $thumb = ThumbsUp::where([
            'user_id' => Auth::id(),
            'content_id' => $work->id,
            'type' => 'work'
        ])->first(['id']);
        $work->is_like = !empty($thumb) ? 1 : 0;
        return $this->response()->success($work);
    }

    /**
     * 作业列表
     */
    public function workList(Request $request)
    {
        $user = Auth::user();
        $data = Work::where('user_id', $user->id)
            ->title($request->title)
            ->lesson($request->lesson_id)
            ->type($request->type)
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($data as $vo) {
            $vo->file_url = $vo->json_url;
            $vo->image_cover = $vo->image_url;
        }
        return $this->response()->collection($data, WorkResource::class);
    }

    /**
     * 作品点赞
     */
    public function thumbsUp(Work $work, Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type');
        if ($user) {          //登录点赞
            $thumbs_up = ThumbsUp::where([
                'user_id' => $user->id,
                'type' => 'work',
                'content_id' => $work->id,
            ])->first();
            if (empty($thumbs_up)) {
                $work->increment('likes');
                ThumbsUp::create(['user_id' => $user->id, 'type' => 'work', 'content_id' => $work->id]);
            } elseif ($work->likes > 0) {
                $thumbs_up->delete();
                $work->decrement('likes');
                return $this->response()->success('取消');
            }
        } else {
            if ($type == 1) {
                $work->increment('likes');
            } elseif ($type == 2 && $work->likes > 0) {
                $work->decrement('likes');
                return $this->response()->success('取消');
            }
        }
        return $this->response()->success('成功');
    }

    /**
     * 作品浏览
     */
    public function browse(Work $work)
    {
        $work->increment('views');
        return $this->response()->success('成功');
    }

    /**
     * 记录分享作品
     */
    public function share(Work $work)
    {
        if (request()->user()->id == $work->user_id) {
            $work->share = 1;
            $work->save();
        }
        return $this->response()->success('成功');
    }

    /**
     * 主题下作业数量
     */
    public function lessonWorkList()
    {
        $data = Work::where([
            'status' => Work::WORK_STATUS_ON,
            'user_id' => request()->user()->id,
        ])
            ->with([
                'lesson' => function ($query) {
                    $query->with(['course' => function ($query) {
                        $query->select('id', 'title', 'category');
                    }, 'cover'])
                        ->select('id', 'title', 'course_id', 'cover_id')
                        ->where('status', CourseLesson::LESSON_STATUS_ON);
                }])
            ->groupBy('lesson_id')
            ->select(DB::raw("count(*) as num"), 'lesson_id')
            ->get();
        $tmp = [];
        foreach ($data as $key => $vo) {
            if (!empty($vo->lesson->course) && $vo->lesson->course->category == 2) {
                $lessons = [
                    'id' => $vo->lesson->id,
                    'title' => $vo->lesson->title,
                    'work_count' => $vo->num,
                    'cover' => $vo->lesson->cover->toarray()
                ];
                if (empty($tmp[$vo->lesson->course->id])) {
                    $tmp[$vo->lesson->course->id] = [
                        'id' => $vo->lesson->course->id,
                        'title' => $vo->lesson->course->title,
                    ];
                }
                $tmp[$vo->lesson->course->id]['lessons'][] = $lessons;
            }
        }
        return $this->response()->success(array_values($tmp));
    }

    //处理上传sb3文件
    private function handle_zip($file)
    {
        if (!empty($file)) {
            $za = new \ZipArchive();
            $tmpdir = tempdir();
            $za->open($file->getRealPath());
            $za->extractTo($tmpdir);
            $json_path = '';
            for ($i = 0; $i < $za->numFiles; ++$i) {
                $name = $za->getNameIndex($i);
                $file_dir = $tmpdir . DIRECTORY_SEPARATOR . $name;  //解压后文件名
                $md5 = md5_file($file_dir);   //md5规则加密
                $ext = pathinfo($name, PATHINFO_EXTENSION);   //获取文件扩展
                $file_name = $md5 . '.' . $ext;         //云上文件名
                if ($name == 'project.json') {
                    $json_path = 'dn/work/' . date('Y-m-d') . '/' . uniqueName() . '.' . $ext;
                    Storage::disk(config('filesystems.cloud'))->put($json_path, file_get_contents($file_dir));
                } else {
                    $new_path = 'dn/media/' . $file_name;
                    Storage::disk(config('filesystems.cloud'))->put($new_path, file_get_contents($file_dir));
                }
            }
            File::deleteDirectory($tmpdir);
            return $json_path;
        }
        return '';
    }

    private function uploadFile($file)
    {
        if (!empty($file)) {
            $new_name = uniqueName() . '.' . $file->getClientOriginalExtension();
            $new_path = 'dn/image/' . date('Y-m-d') . '/' . $new_name;
            Storage::disk(config('filesystems.cloud'))->put($new_path, file_get_contents($file->getRealPath()));
            return $new_path;
        }
        return '';
    }

}
