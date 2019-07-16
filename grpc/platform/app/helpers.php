<?php

declare(strict_types=1);

namespace App;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

if (!function_exists('tempdir')) {
    /**
     * 临时文件夹
     *
     * @param string $dir
     * @param string $prefix
     * @return string|boolen
     */
    function tempdir($dir = '', $prefix = 'php')
    {
        $dir = $dir ? $dir : sys_get_temp_dir();
        $tempfile = tempnam($dir, $prefix);
        if (file_exists($tempfile)) {
            unlink($tempfile);
        }

        mkdir($tempfile);

        return is_dir($tempfile) ? $tempfile : false;
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

        return (bool) preg_match('/^(\+?0?86\-?)?1[3-9]\d{9}$/', $number);
    }
}

if (!function_exists('isBase64')) {
    /**
     * 判断字符串是否base64编码
     *
     * @param string $number
     * @return bool
     */
    function isBase64(?string $str): bool
    {
        if (empty($str)) {
            return false;
        }

        $str = Str::after($str, 'base64,');

        return $str == base64_encode(base64_decode($str));
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
            ], $data))->setStatusCode($statusCode);
        } else {
            return response()->json(array_merge([
                'message' => $msg,
                'code' => $statusCode,
            ], $data))->setStatusCode($statusCode);
        }
    }
}

if (!function_exists('iteratorGet')) {
    /**
     * 获取数组或对象中的某个元素 如果存在指定元素则返回元素，否则返回默认值(如果默认值是一个异常，则抛出)
     *
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
     *
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
     *
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

