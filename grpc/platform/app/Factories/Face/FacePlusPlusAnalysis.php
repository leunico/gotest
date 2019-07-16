<?php

declare(strict_types=1);

namespace App\Factories\Face;

use App\Factories\Face\Contracts\FaceHandleInterface;
use Illuminate\Http\UploadedFile;
use App\Factories\Face\Traits\FaceClient;
use App\Traits\FileHandle;
use Illuminate\Support\Facades\Log;

class FacePlusPlusAnalysis implements FaceHandleInterface
{
    use FaceClient, FileHandle;

    // 人脸识别
    const DETECT_API = 'https://api-cn.faceplusplus.com/facepp/v3/detect';
    // 需要分析的项目
    const DETECT_PROJECT = [
        'gender',   # 性别分析
        'smiling',  # 笑容分析
        'eyegaze',  # 眼球位置与视线方向信息
        'blur',     # 人脸模糊分析结果
        'headpose', # 人脸姿势分析结果
        'age',      # 年龄分析结果
        'beauty'    # 颜值识别结果
    ];

    // 人脸比对
    const CPMPARE_API = 'https://api-cn.faceplusplus.com/facepp/v3/compare';

    /**
     * handle detect.
     *
     * @param \Illuminate\Http\UploadedFile|string $imageFile
     * @return array
     */
    public function detect($imageFile): array
    {
        $result = $imageFile instanceof UploadedFile ? $this->httpUpload(
            self::DETECT_API,
            ['image_file' => $imageFile->getRealPath()],
            ['return_attributes' => implode(',', self::DETECT_PROJECT)]
        ) : $this->httpPost(
            self::DETECT_API,
            ['image_base64' => $imageFile, 'return_attributes' => implode(',', self::DETECT_PROJECT)]
        );

        if (isset($result['error_message']) || ! isset($result['faces'])) {
            return isset($result['status']) ? $result : ['status' => Face::FACE_ANALYSIS_ERROR, 'error_message' => $result['error_message']];
        }

        $fileModel = $imageFile instanceof UploadedFile ?
            $this->uploadStore($imageFile) :
            $this->uploadFileBase64(str_replace('data:image/png;base64,', '', $imageFile), $result['image_id'] . '.png');

        $result['file_id'] = $fileModel ? $fileModel->id : 0;
        return $result;
    }

    /**
     * handle compare.
     *
     * @param string $faceToken
     * @param string $photo
     * @return array
     */
    public function compare(string $faceToken, string $photo): array
    {
        $result = $this->httpPost(
            self::CPMPARE_API,
            ['face_token1' => $faceToken, 'image_url2' => $photo]
        );

        if (isset($result['error_message']) && ! isset($result['confidence'])) {
            return isset($result['status']) ? $result : ['status' => Face::FACE_ANALYSIS_ERROR, 'error_message' => $result['error_message']];
        }

        return $result;
    }
}
