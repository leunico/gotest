<?php

namespace App\Factories\Face;

/**
 * Face Response represents an HTTP response.
 *
 * @author lizx
 */
class Face
{
    const SUCCESS = 0; // 成功
    const HTTP_REQUEST_GUZZLE = 100400; // 请求face++错误
    const HTTP_REQUEST_ERROR = 100500;  // 请求过程抛出异常
    const FACE_HANDLE_ERROR = 200500; // 服务器处理错误
    const FACE_ANALYSIS_ERROR = 200405; // 返回失败或者接口返回数据有误
    const FACE_ANALYSIS_COMPARE = 200403;  // 人脸比对失败
    const FACE_ANALYSIS_NOTFOUNT = 200404;  // 没有检测到人脸
    const FACE_ANALYSIS_MANY = 200405;  // 多张人脸
    const FACE_ANALYSIS_HEADPOSE_YAWANGLE = 200420;  // 考试转头了
    const FACE_ANALYSIS_HEADPOSE_PITCHANGLE_UP = 200421;  // 考试抬头了
    const FACE_ANALYSIS_HEADPOSE_PITCHANGLE_DOWN = 200422;  // 考试低头了

    public static $tips = [
        self::FACE_ANALYSIS_NOTFOUNT => '检测到您已离开摄像头，请遵守考试规则，回到座位完成答题，否则会被记录作弊嫌疑！',
        self::FACE_ANALYSIS_MANY => '检测到多人出现在摄像头，请遵守考试规则，独立完成考试，否则会被记录作弊嫌疑！',
        self::FACE_ANALYSIS_COMPARE => '检测到非本人出现在摄像头，请遵守考试规则，独立完成考试，否则会被记录作弊嫌疑！'
    ];
}
