<?php

namespace Modules\Personal\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use function App\tempdir;
use function App\uniqueName;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class HandleWorkSbFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $work_sbfile;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($work_sbfile)
    {
        $this->work_sbfile = $work_sbfile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tmpdir = tempdir();
        $path = Storage::disk(config('filesystems.cloud'))->url($this->work_sbfile->sb_url);
        $sb_file = $tmpdir . DIRECTORY_SEPARATOR . uniqueName() . '.sb3';
        $res = \copy($path, $sb_file);
        if (!$res) {
            Log::info("异步处理sb文件素材：失败");
            return false;
        }
        $za = new \ZipArchive();
        $za->open($sb_file);
        $za->extractTo($tmpdir);
        for ($i = 0; $i < $za->numFiles; ++$i) {
            $name = $za->getNameIndex($i);
            $file_dir = $tmpdir . DIRECTORY_SEPARATOR . $name;  //解压后文件名
            $md5 = md5_file($file_dir);   //md5规则加密
            $ext = pathinfo($name, PATHINFO_EXTENSION);   //获取文件扩展
            $file_name = $md5 . '.' . $ext;         //md5文件名
            if ($name != 'project.json') {
                $new_path = 'scratch/media/' . $file_name;
                if (!Redis::sismember('material', $file_name)) {
                    Redis::sadd('material', $file_name);
                    if (!Storage::disk(config('filesystems.cloud'))->has($new_path)) {
                        Storage::disk(config('filesystems.cloud'))->put($new_path, file_get_contents($file_dir));
                    }
                }
            }
        }
        $za->close();
        File::deleteDirectory($tmpdir);
        $this->work_sbfile->is_handle = 1;
        $this->work_sbfile->save();
    }
}
