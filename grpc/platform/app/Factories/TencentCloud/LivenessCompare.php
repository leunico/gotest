<?php

declare(strict_types=1);

namespace App\Factories\TencentCloud;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\FileHandle;
use TencentCloud\Faceid\V20180301\Models\LivenessCompareRequest;
use Modules\Examinee\Entities\Examinee;
use Intervention\Image\Facades\Image;

class LivenessCompare extends Client
{
    use FileHandle;

    /**
     * 人脸比对.
     *
     * @param string $videoFile
     * @param \Modules\Examinee\Entities\Examinee $user
     * @return array
     */
    public function handle(string $videoFile, Examinee $user): array
    {
        $req = new LivenessCompareRequest();
        $req->ImageBase64 = $this->toBase64($user->photo);
        $req->VideoBase64 = $videoFile;
        $req->LivenessType = 'SILENT'; // 先静默模式吧
        $req->ValidateData = null;
        $resp = $this->client()->LivenessCompare($req);

        $respAry = json_decode($resp->toJsonString(), true);
        if (isset($respAry['BestFrameBase64']) && ! empty($respAry['BestFrameBase64'])) {
            $respAry['BestFile'] = $this->uploadFileBase64($respAry['BestFrameBase64'], $respAry['RequestId'] . '.jpg');
        }

        return $respAry;
    }

    /**
     * 转 base64.
     *
     * @param string $url
     * @return array
     */
    public function toBase64(string $url): string
    {
        // $img = Image::make($url)->encode('data-url');

        // return $img->encoded;

        return base64_encode(file_get_contents($url));
    }
}
