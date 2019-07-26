<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

if (! function_exists('arrayKeyLast')) {
    /**
     * Polyfill for array_key_last() function added in PHP 7.3.
     * Get the last key of the given array without affecting
     * the internal array pointer.
     *
     * @param array $array An array
     * @return mixed The last key of array if the array is not empty; NULL otherwise.
     */
    function arrayKeyLast($array)
    {
        $key = null;

        if (is_array($array)) {
            end($array);
            $key = key($array);
        }

        return $key;
    }
}

if (! function_exists('arrayKeyFirst')) {
    /**
     * Polyfill for array_key_first() function added in PHP 7.3.
     * Gets the first key of an array
     *
     * @param array $array
     * @return mixed
     */
    function arrayKeyFirst(array $array)
    {
        if (count($array)) {
            reset($array);
            return key($array);
        }

        return null;
    }
}

if (!function_exists('validateChinaPhoneNumber')) {
    /**
     * 验证是否是中国手机号
     *
     * @param string $number
     * @return bool
     */
    function validateChinaPhoneNumber(?string $number): bool
    {
        if (empty($number)) {
            return false;
        }

        return (bool)preg_match('/^(\+?0?86\-?)?1[3-9]\d{9}$/', $number);
    }
}

if (!function_exists('validateUsername')) {
    /**
     * 验证用户名是否合法.
     *
     * @param string $username
     * @return bool
     */
    function validateUsername(?string $username): bool
    {
        if (empty($username)) {
            return false;
        }

        return (bool)preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $username);
    }
}

if (!function_exists('validateDisplayLength')) {
    /**
     * 验证显示长度计算.
     *
     * @param string|int $value
     * @param array $parameters
     * @return bool
     * @author lizx
     */
    function validateDisplayLength(?string $value, array $parameters): bool
    {
        if (empty($value)) {
            return false;
        }

        preg_match_all('/[a-zA-Z0-9_]/', $value, $single);
        $length = count($single[0]) / 2 + mb_strlen(preg_replace('([a-zA-Z0-9_])', '', $value));

        return validateBetween($length, $parameters);
    }
}

if (!function_exists('validateDisplayWidth')) {
    /**
     * 验证中英文显示宽度.
     *
     * @param string $value
     * @param array $parameters
     * @return bool
     * @author lizx
     */
    function validateDisplayWidth(?string $value, array $parameters): bool
    {
        if (empty($value)) {
            return false;
        }

        $number = strlen(mb_convert_encoding($value, 'GB18030', 'UTF-8'));

        return validateBetween($number, $parameters);
    }
}

if (!function_exists('validateBetween')) {
    /**
     * 验证一个数字是否在指定的最小最大值之间.
     *
     * @param float $number
     * @param array $parameters
     * @return bool
     * @author lizx
     */
    function validateBetween(float $number, array $parameters): bool
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Parameters must be passed');
        }

        list($min, $max) = array_pad($parameters, -2, 0);

        return $number >= $min && $number <= $max;
    }
}

if (!function_exists('username')) {
    /**
     * Get user login field.
     *
     * @param string $login
     * @param string $default
     * @return string
     * @author lizx
     */
    function username(?string $login, string $default = 'id'): string
    {
        $map = [
            'email' => filter_var($login, FILTER_VALIDATE_EMAIL),
            'phone' => validateChinaPhoneNumber($login),
            'name' => validateUsername($login),
        ];

        foreach ($map as $field => $value) {
            if ($value) {
                return $field;
            }
        }

        return $default;
    }
}

if (!function_exists('responseSuccess')) {
    /**
     * Success response
     *
     * @param array $data
     * @param string $msg
     * @param array $other
     * @return \Illuminate\Http\JsonResponse
     */
    function responseSuccess($data = [], $msg = 'Success.', $other = [])
    {
        $res = [
            'message' => $msg,
            'code' => 200,
            // 'status' => 'OK',
            'data' => $data,
        ];

        $res = !empty($other) ? array_merge($res, $other) : $res;
        if ($data instanceof LengthAwarePaginator) {
            $data = $data->toArray();
            $page = [
                'current_page' => $data['current_page'],
                'last_page' => $data['last_page'],
                'per_page' => $data['per_page'],
                'total' => $data['total'],
            ];

            $res['data'] = $data['data'];
            $res['pages'] = $page;
        }

        return response()->json($res, 200);
    }
}

if (!function_exists('responseFailed')) {
    /**
     * Error response
     *
     * @param string $msg
     * @param integer $statusCode
     * @param array $data
     * @param [type] $debug
     * @return \Illuminate\Http\JsonResponse
     */
    function responseFailed($msg = 'Error.', $statusCode = 400, $data = [])
    {
        if (config('app.debug')) {
            return response()->json(array_merge([
                'message' => $msg,
                'code' => $statusCode,
                // 'status' => 'FAILED',
                // 'data' => $data,
                // 'debug' => $debug,
            ], $data))->setStatusCode($statusCode);
        } else {
            return response()->json(array_merge([
                'message' => $msg,
                'code' => $statusCode,
                // 'status' => 'FAILED',
                // 'data' => $data,
            ], $data))->setStatusCode($statusCode);
        }
    }
}

if (!function_exists('errorLog')) {
    function errorLog(Exception $e, $prefix = '', $level = 'error')
    {
        $fileName = $e->getFile();
        $line = $e->getLine();
        $message = $e->getMessage();
        $msg = "{$prefix} File {$fileName}:Line {$line}: {$message}";
        call_user_func_array(['Log', $level], [$msg]);
    }
}

if (!function_exists('iteratorGet')) {
    /**
     * 获取数组或对象中的某个元素 如果存在指定元素则返回元素，否则返回默认值(如果默认值是一个异常，则抛出)
     * @param array|object $iterator
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     * @throws Exception
     */
    function iteratorGet($iterator, $key, $default = null)
    {
        if (empty($iterator)) {
            if ($default instanceof Exception) {
                throw $default;
            } else {
                return $default;
            }
        }
        if (is_object($iterator)) {
            if (!method_exists($iterator, $key)) {
                if ($iterator instanceof Collection) {
                    return arrGet($iterator, $key, $default);
                }

                return jsonGet($iterator, $key, $default);
            }
            //  对象获取
            return jsonGet($iterator, $key, $default);
        } else {
            //  数组获取
            return arrGet($iterator, $key, $default);
        }
    }
}

if (!function_exists('arrGet')) {
    /**
     * 获取数组中的某个元素
     * @param array|mixed $arr 数组
     * @param mixed $key 下标
     * @param null|mixed $default 默认值
     * @return mixed|null   如果存在指定元素则返回元素，否则返回默认值(如果默认值是一个异常，则抛出)
     * @throws Exception
     */
    function arrGet($arr, $key, $default = null)
    {
        $isDefault = false;
        if (empty($arr) || empty($key) && 0 !== $key) {
            $isDefault = true;
        } else {
            if (!isset($arr[$key])) {
                $isDefault = true;
            }
        }
        if ($isDefault) {
            if ($default instanceof Exception) {
                throw $default;
            } else {
                return $default;
            }
        }

        return $arr[$key];
    }
}

if (!function_exists('jsonGet')) {
    /**
     * 获取json中的某个元素
     * @param object $json json对象
     * @param string $key 下标
     * @param null|mixed $default 默认值
     * @return mixed|null 如果存在指定元素则返回该元素，否则返回默认值(如果默认值是一个异常，则抛出)
     * @throws Exception
     */
    function jsonGet($json, $key, $default = null)
    {
        $isDefault = false;
        if (empty($json) || empty($key)) {
            $isDefault = true;
        } else {
            if (!isset($json->{$key})) {
                $isDefault = true;
            }
        }
        if ($isDefault) {
            if ($default instanceof Exception) {
                throw $default;
            } else {
                return $default;
            }
        }

        return $json->{$key};
    }
}

if (!function_exists('toCarbon')) {
    /**
     * 生成Carbon对象，不合法数据会返回默认值(如果默认值是一个异常，则抛出)
     *
     * @param      $dateTime
     * @param bool $default
     * @return bool|Carbon
     * @throws Exception
     */
    function toCarbon($dateTime = '', $default = false)
    {
        try {
            if ($dateTime instanceof Carbon) {
                return $dateTime;
            }

            return Carbon::parse($dateTime);
        } catch (\Exception $e) {
            if ($default instanceof Exception) {
                throw $default;
            } else {
                return empty($default) ? Carbon::now() : $default;
            }
        }
    }
}

if (!function_exists('toCents')) {
    /**
     * 元转分
     *
     * @param integer $price
     * @return integer
     */
    function toCents($price)
    {
        return intval($price * 1000 / 10);
    }
}

if (!function_exists('tempdir')) {
    /**
     * @param string $dir
     * @param string $prefix
     * @return bool|string
     */
    function tempdir($dir = '', $prefix = 'php')
    {
        $dir = $dir ? $dir : sys_get_temp_dir();
        $tempfile = tempnam($dir, $prefix);
        if (file_exists($tempfile)) {
            unlink($tempfile);
        }
        mkdir($tempfile);
        if (is_dir($tempfile)) {
            return $tempfile;
        }

        return false;
    }
}

if (!function_exists('uniqueName')) {
    //生成唯一id名字
    function uniqueName($len = 20)
    {
        return strtolower(uniqid(str_random($len)));
    }
}

if (!function_exists('arrSortByField')) {
    /**
     * 二维数组按照某个字段排序
     * @param array $arr 要排序的数组
     * @param mixed $field 要排序的字段
     * @param int $arg 排序规则
     * @return array
     */
    function arrSortByField(array $arr, $field, $arg = SORT_ASC)
    {
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $sort[] = $v[$field];
            }
            array_multisort($sort, $arg, $arr);
        }

        return $arr;
    }
}

if (!function_exists('apiVerify')) {
    /**
     * 给请求参数追加鉴权参数
     * @param array $formParams 已有请求参数
     * @param string $project
     * @param string $idType
     * @param string $secretType
     * @throws Exception
     */
    function apiVerify(&$formParams, string $project, $idType = 'crm_id', $secretType = 'crm_secret')
    {
        $nowTime = time();
        $config = config($project);
        $apiAuth = iteratorGet($config, 'auth', new Exception('配置信息错误'));
        $id = isset($apiAuth[$idType]) ? $apiAuth[$idType] : null;
        $secret = isset($apiAuth[$secretType]) ? $apiAuth[$secretType] : null;
        $sig = $id . $secret . $nowTime;
        $authorization = ['id' => $id, 'timestamp' => $nowTime];
        //  装进数组中
        $formParams['sig'] = md5($sig);
        $formParams['authorization'] = base64_encode(json_encode($authorization));
    }
}

if (!function_exists('formatDiv')) {
    /**
     * 四舍五入 格式化除法
     * @param     $divisor
     * @param     $divided
     * @param int $scale
     * @return int|string
     */
    function formatDiv($divisor, $divided, int $scale = 2)
    {
        if (empty((int)$divided)) {
            return sprintf('%.' . $scale . 'f', 0);
        }

        return $scale == 0 ? bcdiv($divisor, $divided, 0) : sprintf(
            '%.' . $scale . 'f',
            bcdiv($divisor, $divided, $scale + 1)
        );
    }
}

if (!function_exists('formatMul')) {
    /**
     * 四舍五入 格式化乘法
     * @param     $leftOperand
     * @param     $rightOperand
     * @param int $scale
     * @return int|string
     */
    function formatMul($leftOperand, $rightOperand, int $scale = 2)
    {
        return $scale == 0 ? bcmul($leftOperand, $rightOperand, 0) : sprintf(
            '%.' . $scale . 'f',
            bcmul($leftOperand, $rightOperand, $scale + 1)
        );
    }
}

if (!function_exists('formatSub')) {
    /**
     * 四舍五入 格式化减法
     * @param     $leftOperand
     * @param     $rightOperand
     * @param int $scale
     * @return int|string
     */
    function formatSub($leftOperand, $rightOperand, int $scale = 2)
    {
        return $scale == 0 ? bcsub($leftOperand, $rightOperand, 0) : sprintf(
            '%.' . $scale . 'f',
            bcsub($leftOperand, $rightOperand, $scale + 1)
        );
    }
}

if (!function_exists('formatAdd')) {
    /**
     *  四舍五入 格式化加法
     * @param     $leftOperand
     * @param     $rightOperand
     * @param int $scale
     * @return int|string
     */
    function formatAdd($leftOperand, $rightOperand, int $scale = 2)
    {
        return $scale == 0 ? bcadd($leftOperand, $rightOperand, 0) : sprintf(
            '%.' . $scale . 'f',
            bcadd($leftOperand, $rightOperand, $scale + 1)
        );
    }
}

if (!function_exists('isMobile')) {
    /**
     * 是否是手机号码
     *
     * @param string $mobile
     * @return bool
     */
    function isMobile(string $mobile): bool
    {
        return preg_match('/^1[3|4|5|6|7|8|9]\\d{9}$/', $mobile) > 0;
    }
}

if (!function_exists('isTradeNo')) {
    /**
     * 是否是订单号ID
     *
     * @param string $mobile
     * @return bool
     */
    function isTradeNo(string $tradeNo): bool
    {
        if (mb_strlen($tradeNo) !== 19) {
            return false;
        }

        return is_numeric(str_limit($tradeNo, 14));
    }
}

/**
 * 请求其他项目的接口(适应新旧接口返回形式)
 * @param string $project 要请求的项目
 * @param string $path 接口路径
 * @param string $method 请求方式，如：POST、GET
 * @param array $params 请求参数
 * @param bool $isArr
 * @return array    [jsonObject|jsonToArray, msg]  msg为错误信息,msg为空即代表本次请求成功返回了接口数据
 * @throws Exception
 */
function requestCodeProject(string $project, $path, $method = 'POST', $params = [], $isArr = false)
{
    $json = null;
    try {
        $config = config($project);
        $baseUri = iteratorGet($config, 'server', new Exception('配置信息错误'));
        $projectName = iteratorGet($config, 'name', new Exception('配置信息错误'));
        $client = new Client();
        //  接口鉴权
        apiVerify($params, $project);
        //  判断需要请求的参数以什么形式传递
        switch (strtoupper($method)) {
            case 'POST':
                $param = ['form_params' => $params];
                break;
            case 'PUT':
            case 'DELETE':
                $param = ['json' => $params];
                break;
            default:
                $param = ['query' => $params];
                break;
        }
        //  拼接接口地址
        $url = $baseUri . $path;
        Log::info('请求' . $projectName . '接口: ' . $url);
        $response = $client->request($method, $url, $param);

        //状态码不是200直接抛出异常
        if (200 != $response->getStatusCode()) {
            Log::info('请求出错！' . $response->getStatusCode());
            throw new Exception($response->getStatusCode());
        }

        //返回的内容不是json,抛出异常
        $json = json_decode($response->getBody()->getContents(), $isArr);
        if (JSON_ERROR_NONE != json_last_error()) {
            Log::info('请求结果异常！' . jsonGet($json, 'msg'));
            throw new Exception(jsonGet($json, 'msg'));
        }

        if ($isArr) {
            $status = (string)arrGet($json, 'status');
            $msg = arrGet($json, 'msg');
        } else {
            $status = (string)jsonGet($json, 'status');
            $msg = jsonGet($json, 'msg');
        }

        //返回的状态无效,抛出异常
        $successStatus = config('services.success_status');
        if (!in_array($status, explode(',', $successStatus), true)) {
            Log::info('请求' . $projectName . '接口出错：' . $msg);
            throw new Exception($msg);
        }

        //成功,将msg设置成null
        $msg = null;
    } catch (ClientException $e) {
        errorLog($e);
        $msg = $e->getResponse()->getBody()->getContents();
        $body = json_decode($msg, $isArr);
        if (JSON_ERROR_NONE == json_last_error()) {
            $msg = $isArr ? arrGet($body, 'msg') : jsonGet($body, 'msg');
        }
    } catch (GuzzleException $g) {
        errorLog($g);
        $msg = $g->getMessage();
    } catch (Exception $e) {
        errorLog($e);
        $msg = $e->getMessage();
    }

    return [$json, $msg];
}

if (!function_exists('formatSecond')) {
    /**
     * 将秒数进行转换
     *
     * @param integer $second
     * @return string
     */
    function formatSecond($secondNumber)
    {
        $step = 60;

        if ($secondNumber < $step) {
            return "{$secondNumber}秒";
        }

        if ($secondNumber < $step * $step) {
            $minute = floor($secondNumber / $step);
            $second = floor($secondNumber % $step);

            return "{$minute}分钟{$second}秒";
        }

        if ($secondNumber < 24 * $step * $step) {
            $hour = floor($secondNumber / ($step * $step));

            $remainderSecond = $secondNumber % ($step * $step);

            $minute = floor($remainderSecond / $step);
            $second = floor($remainderSecond % $step);

            return "{$hour}小时{$minute}分钟{$second}秒";
        }

        $day = floor($secondNumber / (24 * $step * $step));

        $remainderSecond = $secondNumber % (24 * $step * $step);

        $hour = floor($remainderSecond / ($step * $step));

        $remainderSecond = $remainderSecond % ($step * $step);

        $minute = floor($remainderSecond / $step);
        $second = floor($remainderSecond % $step);

        return "{$day}天{$hour}小时{$minute}分钟{$second}秒";
    }
}

if (!function_exists('removeNullElement')) {
    /**
     * 去掉数组中null元素
     * @param array $data
     * @return array
     */
    function removeNullElement(array $data = []): array
    {
        return arrayFilter($data);
    }
}

if (!function_exists('arrayFilter')) {
    /**
     * 去掉数组中指定值所在的元素
     * 如果去除所有等同于false的元素,请直接使用array_filter
     *
     * @param array $data
     * @param bool $needle
     * @return array
     */
    function arrayFilter(array $data = [], $needle = null): array
    {
        return array_filter($data, function ($v) use ($needle) {
            return $v !== $needle;
        });
    }
}

if (!function_exists('uploadFile')) {
    /**
     * 直接把单个文件上传到cdn
     */
    function uploadFile($file, $path = '')
    {
        if ($file->isValid() && $path) {
            $path = trim($path, "/");
            $new_name = uniqueName() . '.' . $file->getClientOriginalExtension();
            $new_path = $path . '/' . $new_name;
            Storage::disk(config('filesystems.cloud'))->put($new_path, file_get_contents($file->getRealPath()));
            return $new_path;
        }
        return '';
    }
}

if (!function_exists('toDecbin')) {
    /**
     * 二进制转化
     *
     * @param integer $value
     */
    function toDecbin(?int $value): array
    {
        $data = [];
        if (empty($value)) {
            return $data;
        }

        $binValue = decbin($value);
        $arrayValue = array_reverse(str_split((string) $binValue));
        foreach ($arrayValue as $key => $value) {
            if (! empty($value)) {
                $data[] = pow(2, $key);
            }
        }

        return $data;
    }
}

if (!function_exists('formatValidationErrors')) {
    function formatValidationErrors($validator)
    {
        $errors = $validator->getMessageBag()->toArray();
        $ret    = '';
        foreach ($errors as $error) {
            $ret .= $error[0] . '  ';
        }

        return $ret;
    }
}

if (!function_exists('getChannel')) {
    function getChannel($default = null)
    {
        $websiteChannel = config('content.website_activity_id');

        if (!empty(session('from_activity_id'))) {
            return session('from_activity_id');
        }

        return $default ? $default : $websiteChannel;
    }
}

