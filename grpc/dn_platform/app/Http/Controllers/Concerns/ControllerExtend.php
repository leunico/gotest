<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

trait ControllerExtend
{
    /**
     * Get sync value
     *
     * @param array $chapters
     * @param int $type
     * @return array
     * @author lizx
     */
    public function getSyncDate(array $syncs, array $variable): array
    {
        $data = [];
        array_map(function ($item) use (&$data, $variable) {
            $data[$item] = $variable;
        }, $syncs);

        return $data;
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
     * 上传自定义文件路径处理
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     * @author lizx
     */
    protected function uploadOther(UploadedFile $file, $path = 'scratch/media/'): array
    {
        $filename = md5_file($file->getRealPath()) . '.' . $file->getClientOriginalExtension();

        if (!Storage::disk(config('filesystems.cloud'))->exists($path . $filename)) {
            Storage::disk(config('filesystems.cloud'))->put($path . $filename, file_get_contents($file->getRealPath()));
        }

        return [
            'name' => $filename,
            'path' => $path
        ];
    }
}
