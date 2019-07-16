<?php

declare(strict_types=1);

namespace App\Factories\TencentCloud;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\FileHandle;
use TencentCloud\Faceid\V20180301\Models\LivenessRecognitionRequest;
use Modules\Examinee\Entities\Examinee;

class LivenessRecognition extends Client
{
    use FileHandle;

    /**
     * 人脸核身.
     *
     * @param string $videoFile
     * @param \Modules\Examinee\Entities\Examinee $user
     * @return array
     */
    public function handle(string $videoFile, Examinee $user): array
    {
        // todo 看情况加
        // try {
        //     // code...
        // } catch (\Exception $e) {
        //     // throw $th;
        // }

        $req = new LivenessRecognitionRequest();
        $req->IdCard = $user->certificates; // '360782199305180838';
        $req->Name = $user->name; // '黎智鑫'
        $req->VideoBase64 = $videoFile;
        $req->LivenessType = 'SILENT'; // 先静默模式吧
        $req->ValidateData = null;
        $resp = $this->client()->LivenessRecognition($req);

        $respAry = json_decode($resp->toJsonString(), true);
        if (isset($respAry['BestFrameBase64']) && ! empty($respAry['BestFrameBase64'])) {
            $respAry['BestFile'] = $this->uploadFileBase64($respAry['BestFrameBase64'], $respAry['RequestId'] . '.jpg');
        }

        return $respAry;
    }
}
