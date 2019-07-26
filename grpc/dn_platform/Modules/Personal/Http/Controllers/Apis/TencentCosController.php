<?php

namespace Modules\Personal\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Qcloud\Cos\Client as TencentCosClient;

/**
 * 腾讯云 COS 相关业务接口
 */
class TencentCosController extends Controller
{
    /**
     * 测试的方法
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            $client = new TencentCosClient([
                'region' => env('COSV5_REGION'),
                'timeout' => 1541498495,
                'credentials' => [
                    'appId' => env('COSV5_APP_ID'),
                    'secretId' => 'AKIDz18fxfCwQEmyHWHZe8Rpb7H8kPHm47rU',
                    'secretKey' => '87t4yImz3X5Hai1B5D1sVm8X1O374lDa',
                    'token' => 'f6408e7720573a5effa8d73e1b81ee80b9eab82930001',
                ],
            ]);
            //dd($client);

            // $response = $client->listBuckets();
            //$response = $client->listObjects(['Bucket' => env('COSV5_BUCKET')]);
            // $response = $client->getObject([
            //     'Bucket' => env('COSV5_BUCKET'),
            //     'Key' => 'SmallClassStudent.php'
            // ]);
//             $response = $client->Upload(
//                 env('COSV5_BUCKET'),
//                 'favicon.ico',
//                 '# Hello World'
//             );

            try {
                $result = $client->putObject([
                    'Bucket' => env('COSV5_BUCKET'),
                    'Key' => 'dn/work/' . date('Y-m-d') . '/' . '123456789.png',
                    'Body' => fopen('./robots.txt', 'rb'), ]);
                //print_r($result);
            } catch (\Exception $e) {
                echo "$e\n";
            }
            //$result = $client->listBuckets();
            dd($result);

            return response()->json($result->toArray());
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * 初始化 client 所需参数
     *
     * @return array
     */
    protected function getConfig()
    {
        return [
            'region' => env('COSV5_REGION'),
            'credentials' => [
                'secretId' => env('COSV5_SECRET_ID'),
                'secretKey' => env('COSV5_SECRET_KEY'),
            ],
        ];
    }

    /**
     * 获取所有存储桶
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBucket(Request $request)
    {
        try {
            $client = $this->getClient();

            $response = $client->listBuckets();

            return response()->json($response->toArray());
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * 获取一个 Tencent COS Client
     *
     * @return TencentCosClient
     */
    protected function getClient()
    {
        return new TencentCosClient($this->getConfig());
    }

    /**
     * 申请临时密钥三元组
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTempSecret(Request $request)
    {
        $config = [
            'SecretId' => config('filesystems.disks.cosv5.credentials.secretId'),
            'SecretKey' => config('filesystems.disks.cosv5.credentials.secretKey'),
            'RequestMethod' => 'GET',
            'DefaultRegion' => config('filesystems.disks.cosv5.region'),
        ];
        $api = \QcloudApi::load(\QcloudApi::MODULE_STS, $config);
        //dd($config);
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
            $error = $api->getError();

            return $this->response()->error($error->getMessage());
        } else {
            return $this->response()->success($response['data']);
        }
    }

    /**
     * 通过cdn上传作业
     */
    public function cdnSaveWork(WorkCdnRequest $request)
    {
        $user = Auth::user();
        $formData = $request->only(['title', 'lesson_id', 'description', 'image_cover', 'file', 'board_type', 'sb_url', 'work_id']);
        $formData = removeNullElement($formData);
        //作品信息
        $formData['user_id'] = $user->id;
        if ($request->hasFile('file')) {
            $formData['file_url'] = uploadFile($request->file('file'), 'dn/work/' . date('Y-m-d'));
        }
        $up = false;
        if ($request->hasFile('image_cover')) {
            $up = true;
            $formData['image_cover'] = uploadFile($request->file('image_cover'), 'dn/image/' . date('Y-m-d'));
        }
        $formData['type'] = 'scratch_arduino';
        $sb_url = $formData['sb_url'];
        unset($formData['file']);
        unset($formData['sb_url']);
        if (!empty($formData['work_id'])) {
            $work_data = Work::findOrFail($formData['work_id']);
            unset($formData['work_id']);
            if (!$up) {
                unset($formData['image_cover']);
            }
            $work_data->update($formData);
        } else {
            $work_data = Work::create($formData);
        }
        //异步处理sb3文件上传素材
        $sbfile = [
            'user_id' => $user->id,
            'work_id' => $work_data->id,
            'sb_url' => trim($sb_url, '/')
        ];
        $work_sbfile = WorkSbfile::create($sbfile);
        $this->dispatch(new HandleWorkSbFile($work_sbfile));

        return $this->response()->success($work_data);
    }

}
