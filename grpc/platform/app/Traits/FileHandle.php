<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\File as FileModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use function App\tempdir;
use Illuminate\Support\Str;

trait FileHandle
{

    /**
     * upload file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function uploadStore(UploadedFile $file): ?FileModel
    {
        $fileModel = $this->validateFileInDatabase($fileModel = new FileModel, $file, function (UploadedFile $file, string $md5) use ($fileModel): FileModel {
            list($width, $height) = ($imageInfo = @getimagesize($file->getRealPath())) === false ? [0, 0] : $imageInfo;
            $path = Carbon::now()->format('Y/m/d/Hi');
            if (($filename = $file->store($path, config('filesystems.cloud'))) === false) {
                return $fileModel;
            }

            $fileModel->filename = $filename;
            $fileModel->hash = $md5;
            $fileModel->origin_filename = $file->getClientOriginalName();
            $fileModel->mime = $file->getClientMimeType();
            $fileModel->width = $width;
            $fileModel->height = $height;
            $fileModel->driver_baseurl = $this->getDiskUrl();
            $fileModel->saveOrFail();

            return $fileModel;
        });

        return $fileModel->id ? $fileModel : null;
    }

    /**
     * Validate and return the file database model instance.
     *
     * @param \App\File $fileModel
     * @param \Illuminate\Http\UploadedFile $file
     * @param callable $call
     * @return \App\File
     * @author lizx
     */
    protected function validateFileInDatabase(FileModel $fileModel, UploadedFile $file, callable $call): FileModel
    {
        $hash = md5_file($file->getRealPath());

        return $fileModel->where('hash', $hash)->firstOr(function () use ($file, $call, $hash): FileModel {
            return call_user_func_array($call, [$file, $hash]);
        });
    }

    /**
     * 返回disk域名链接
     *
     * @param string $path
     * @return string
     * @author lizx
     */
    protected function getDiskUrl($path = '/')
    {
        return Storage::disk(config('filesystems.cloud'))->url($path);
    }

    /**
     * 上传非标准类型文件
     *
     * @param string $file
     * @param string $fileName
     * @param string $path
     * @return \App\File
     */
    public function uploadFileBase64(string $file, string $fileName, string $originalName = 'Base64File', string $mimeType = 'image/jpg', ?string $path = null): FileModel
    {
        $hash = md5($file);
        $fileModel = FileModel::where('hash', $hash)->firstOr(function () use ($file, $hash, $fileName, $originalName, $mimeType, $path): FileModel {
            $path = $path ?? Carbon::now()->format('Y/m/d/');
            if (! Storage::disk(config('filesystems.cloud'))->exists($path . $fileName)) {
                Storage::disk(config('filesystems.cloud'))->put($path . $fileName, base64_decode($file));
            }

            $fileModel = new FileModel;
            $fileModel->filename = $path . $fileName;
            $fileModel->hash = $hash;
            $fileModel->origin_filename = $originalName;
            $fileModel->mime = $mimeType;
            $fileModel->width = 0;
            $fileModel->height = 0;
            $fileModel->driver_baseurl = $this->getDiskUrl();
            $fileModel->saveOrFail();

            return $fileModel;
        });

        return $fileModel->id ? $fileModel : null;
    }

    /**
     * 处理预加载文件
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     * @author lizx
     */
    protected function handleCodeFile(UploadedFile $file): array
    {
        if ($file->guessExtension() == 'zip') {
            return $this->resetAnswerFile($file);
        }

        if ($fileModel = $this->uploadStore($file)) {
            return [
                'name' => $fileModel->origin_filename,
                'json_url' => $fileModel->driver_baseurl . $fileModel->filename
            ];
        } else {
            return [];
        }
    }

    /**
     * 处理考试考生文件[作品]
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @return array
     * @author lizx
     */
    protected function resetAnswerFile(UploadedFile $file, string $path = ''): array
    {
        $za = new \ZipArchive();
        $tmpdir = tempdir();
        $za->open($file->path());
        $za->extractTo($tmpdir);

        $result = [];
        for ($i = 0; $i < $za->numFiles; $i++) {
            $name = $za->getNameIndex($i);
            if ($name == 'project.json') { // Str::contains($name, '.json')
                $newPath = $this->mediaPath(Carbon::now()->format('Y/m/d/') . auth()->user()->id . '_' . substr(microtime(), 2, 6) . Str::random(10) . $name);
                $result['name'] = $file->getClientOriginalName();
                $result['json_url'] = $this->getDiskUrl($newPath);
            } elseif (strlen($name) < 16) {
                $md5 = md5_file($tmpdir . DIRECTORY_SEPARATOR . $name);
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newPath = $this->mediaPath($path . DIRECTORY_SEPARATOR . $md5 . '.' . $ext);
            } else {
                $newPath = $this->mediaPath($name);
            }
            $fileDir = $tmpdir . DIRECTORY_SEPARATOR . $name;
            if (! Storage::disk(config('filesystems.cloud'))->exists($newPath)) {
                Storage::disk(config('filesystems.cloud'))->put($newPath, file_get_contents($fileDir));
            }
        }

        File::deleteDirectory($tmpdir);
        return $result;
    }

    /**
     * 将MD5与路径合并生成绝对路径
     *
     * @param $name
     * @return string
     * @author lizx
     */
    protected function mediaPath(string $name = ''): string
    {
        return 'bluebridge/material/' . $name;
    }
}
