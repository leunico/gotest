<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Symfony\Component\HttpKernel\Exception;
use Intervention\Image\ImageManagerStatic as Image;
use Carbon\Carbon;
use App\File as FileModel;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;
use App\Http\Requests\StoreUploadFile as StoreUploadFileRequest;
use App\Http\Controllers\Concerns\ControllerExtend;
use Illuminate\Http\JsonResponse;
use function App\responseSuccess;
use function App\responseFailed;

class FilesController extends Controller
{
    use ControllerExtend;

    /**
     * Get file.
     *
     * @param \Illuminate\Http\Request $request
     * @param FileModel $file
     * @return mixed
     * @author lizx
     */
    public function show(Request $request, FileModel $file)
    {
        return $request->query('json') !== null
            ? responseSuccess(asset($file->driver_baseurl . $file->filename))
            : redirect()->to(asset($file->driver_baseurl . $file->filename));
    }

    /**
     * upload file
     *
     * @param \App\Http\Requests\StoreUploadFile $request
     * @param ResponseContract $response
     * @param \App\File $fileModel
     * @param \Carbon\Carbon $dateTime
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreUploadFileRequest $request, ResponseContract $response, FileModel $fileModel, Carbon $dateTime): JsonResponse
    {
        $clientHeight = $request->input('height', 0); // dd($request->file('file')->guessExtension());
        $clientWidth = $request->input('width', 0);

        $fileModel = $this->validateFileInDatabase($fileModel, $file = $request->file('file'), function (UploadedFile $file, string $md5) use ($fileModel, $dateTime, $clientWidth, $clientHeight, $response): FileModel {
            list($width, $height) = ($imageInfo = @getimagesize($file->getRealPath())) === false ? [null, null] : $imageInfo;
            $path = $dateTime->format('Y/m/d/Hi');
            if (($filename = $file->store($path, config('filesystems.cloud'))) === false) {
                return responseFailed('上传失败', 500);
            }
            if (config('filesystems.act_resizs') && !empty($width) && !empty($height) && in_array($file->getClientMimeType(), ['image/png', 'image/jpeg'])) {
                $this->ImageCopyResiz($filename, $file);
            }

            $fileModel->filename = $filename;
            $fileModel->hash = $md5;
            $fileModel->origin_filename = $file->getClientOriginalName();
            $fileModel->mime = $file->getClientMimeType();
            $fileModel->width = $width ?? $clientWidth;
            $fileModel->height = $height ?? $clientHeight;
            $fileModel->driver_baseurl = $this->getDiskUrl();
            $fileModel->saveOrFail();

            return $fileModel;
        });

        return responseSuccess([
            'id' => $fileModel->id,
            'url' => $fileModel->driver_baseurl . $fileModel->filename
        ], '上传成功');
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
     * Image copy resized
     *
     * @param [string] $path
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     * @author lizx
     */
    protected function ImageCopyResiz($path, UploadedFile $file)
    {
        $img = Image::make($file->getRealPath());
        foreach (config('filesystems.resizs') as $key => $value) {
            $img->resize($value['width'], $value['height']);
            $img->save($file->getRealPath());
            if (Storage::disk(config('filesystems.cloud'))->put($key . '/' . $path, file_get_contents($file->getRealPath())) === false) {
                throw new HttpException(500, '压缩图片上传失败');
            }
        }
    }
}
