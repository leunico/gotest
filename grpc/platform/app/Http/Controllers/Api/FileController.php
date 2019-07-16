<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\FileHandle;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreUploadFileRequest;

class FileController extends Controller
{
    use FileHandle;

    /**
     * Upload File
     *
     * @param \App\Http\Requests\StoreUploadFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreUploadFileRequest $request): JsonResponse
    {
        // dd($request->file('file')->guessExtension(), $request->file('file')->getClientOriginalExtension());
        $fileM = $this->uploadStore($request->file('file'));

        return empty($fileM) ? $this->response()->error() : $this->response()->success([
            'id' => $fileM->id,
            'url' => $fileM->driver_baseurl . $fileM->filename
        ]);
    }

    /**
     * 申请Cov临时密钥三元组
     *
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function getTxSecret()
    {
        $config = [
            'SecretId' => config('filesystems.disks.cosv5.credentials.secretId'),
            'SecretKey' => config('filesystems.disks.cosv5.credentials.secretKey'),
            'RequestMethod' => 'GET',
            'DefaultRegion' => config('filesystems.disks.cosv5.region'),
        ];
        $api = \QcloudApi::load(\QcloudApi::MODULE_STS, $config);
        $policy = [
            'version' => '2.0',
            'statement' => [
                [
                    'action' => 'cos:*',
                    'effect' => 'allow',
                    'resource' => [
                        'qcs::cos:' . config('filesystems.disks.cosv5.region') . ':uid/' . config('filesystems.disks.cosv5.credentials.appId') . ':prefix//' . config('filesystems.disks.cosv5.credentials.appId') . '/*',
                    ],
                ],
            ],
        ];
        $package = [
            'SignatureMethod' => 'HmacSHA256',
            'policy' => json_encode($policy, JSON_UNESCAPED_SLASHES),
            'name' => config('filesystems.disks.cosv5.bucket'),
        ];

        $response = $api->GetFederationToken($package);
        if ($response === false) {
            return $this->response()->error($api->getError()->getMessage());
        } else {
            return $this->response()->success($response['data']);
        }
    }
}
