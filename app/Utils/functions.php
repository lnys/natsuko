<?php
/**
 * @Author: Ali2vu <751815097@qq.com>
 * @Date: 2019-12-20 11:30:18
 * @LastEditors: Ali2vu
 * @LastEditTime: 2020-01-11 18:18:22
 */

declare(strict_types=1);

/**
 * 容器实例
 */

use Hyperf\Nsq\Nsq;
use App\Constants\BizCode;
use App\Service\MessageService;
use App\Exception\ApiException;
use App\Amqp\Producer\MessageProducer;
use App\Amqp\Producer\WebSocketProducer;
use App\Controller\Ws\Cache;
use Hyperf\Amqp\Producer;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;

if (!function_exists('container')) {
    function container()
    {
        return ApplicationContext::getContainer();
    }
}

if (!function_exists('di')) {
    function di($id = null)
    {
        $container = ApplicationContext::getContainer();
        if ($id) {
            return $container->get($id);
        }

        return $container;
    }
}

if (!function_exists('sf')) {
    function sf()
    {
        $generate = ApplicationContext::getContainer()->get(IdGeneratorInterface::class);
        return $generate->generate();
    }
}

/**
 * 缓存实例 简单的缓存
 */
if (!function_exists('cache')) {
    function cache()
    {
        return container()->get(Psr\SimpleCache\CacheInterface::class);
    }
}

/**
 * 发送ws消息
 */
if(!function_exists('sendWs')) {
    function sendWs($userId, $type, $value)
    {
        if (empty($userId)) {
            return;
        }
        $producer = di()->get(Producer::class);
        if (!is_array($userId)) {
            $cacheUser = Cache::cacheUser($userId);
            if ($cacheUser) {
                $message = new WebSocketProducer($userId, $cacheUser['fd'], $cacheUser['key'], $type, $value);
                $producer->produce($message);
            }
        } else {
            foreach($userId as $user) {
                $cacheUser = Cache::cacheUser($user);
                if ($cacheUser) {
                    $message = new WebSocketProducer($user, $cacheUser['fd'], $cacheUser['key'], $type, $value);
                    $producer->produce($message);
                }
            }
        }

    }
}

/**
 * 发送amqp消息
 */
if(!function_exists('sendAMQP')) {
    function sendAMQP(string $name, array $data = [])
    {
        $producer = di()->get(Producer::class);
        if ($name === "message") {
            $message = New MessageProducer(['hello' => "world"]);
            $producer->produce($message);
        }
    }
}

/**
 * 发送nsq消息
 */
if(!function_exists('sendNSQ')) {
    function sendNSQ(string $topic, $data, float $deferTime = 0.0)
    {
        try {
            retry(1, function() use ($topic, $data, $deferTime) {
                R("发送NSQ消息${topic}");
                $nsq = di()->get(Nsq::class);
                $nsq->publish($topic, json_encode($data, JSON_UNESCAPED_UNICODE), $deferTime);
                R("发送NSQ消息${topic}->publish");
            });
        } catch (Throwable $e) {
            R($e->getMessage(), "NSQ发送失败");
        }
    }
}

/**
 * 批量发送NSQ消息
 * @author Ali2vu <751815097@qq.com>
 * @param string $topic
 * @param $data
 */
if(!function_exists('sendBatchNSQ')) {
    function sendBatchNSQ(string $topic, $data, $deferTime = 0.0)
    {
        $data = is_string($data) ? [$data] : $data;
        array_map(function($v) use($topic, $deferTime) {
            if (is_array($v)) {
                sendNSQ($topic, $v, $deferTime);
            }
        }, $data);
    }
}

/**
 * 字符串截取
 */
if (!function_exists('gtSubstr')) {
    function gtSubstr($str, $length = 30, $append = true)
    {
        $str = trim($str);
        $strLength = strlen($str);
        if ($length == 0 || $length >= $strLength || $length < 0) {
            return $str;
        }

        if (function_exists('mb_substr')) {
            $newstr = mb_substr($str, 0, $length, 'utf-8');
        } else if (function_exists('iconv_substr')) {
            $newstr = iconv_substr($str, 0, $length, 'utf-8');
        } else {
            $newstr = substr($str, 0, $length);
        }

        if ($append && $str != $newstr){
            $newstr .= '...';
        }
        return $newstr;
    }
}

/**
 * 获取IP
 */
if (!function_exists('getip')) {
    function getip()
    {
        $request = di()->get(RequestInterface::class);
        if ($realip = $request->header('remoteip')) {
            return $realip;
        }

        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($arr as $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '0.0.0.0';
            }
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }

        preg_match('/[\\d\\.]{7,15}/', $realip, $onlineip);
        $realip = (!empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0');
        return $realip;
    }
}

/**
 * 批量检查参数在另一个数组是否合法
 */
if (!function_exists('CheckArrIntersect')) {
    function CheckArrIntersect($params, array $mod = [], $message = "缺少请求参数")
    {
        if (empty($params)) {
            throw new ApiException(BizCode::ERROR_REQUEST_CODE, $message);
        }

        foreach ($mod as $key => $value) {
            $tmp = $params[$value] ?? 0;
            if (!$tmp) {
                throw new ApiException(BizCode::ERROR_REQUEST_CODE, $message);
            }
        }
    }
}

/**
 * 批量检查参数是否合法
 */
if (!function_exists('CheckArrEmpty')) {
    function CheckArrEmpty($params, $message = "请求参数不能为空")
    {
        if (empty($params)) {
            throw new ApiException(BizCode::ERROR_REQUEST_CODE, $message);
        }

        foreach ($params as $key => $value) {
            if (!$value) {
                throw new ApiException(BizCode::ERROR_REQUEST_CODE, $message);
            }
        }
    }
}

/**
 * 批量检查参数是否合法
 */
if (!function_exists('CheckArrInside')) {
    function CheckArrInside($params, $mod, $message = "请求参数不能为空")
    {
        if (empty($params)) {
            throw new ApiException(BizCode::ERROR_REQUEST_CODE, $message);
        }

        if (empty($mod)) {
            throw new ApiException(BizCode::ERROR_REQUEST_CODE, $message);
        }

        if (!in_array($mod, $params)) {
            throw new ApiException(BizCode::ERROR_REQUEST_CODE, $message);
        }
    }
}

/**
 * 批量检查参数是否合法
 */
if (!function_exists('ToArray')) {
    function ToArray($data)
    {
        if (!$data) return [];
        if (is_object($data)) {
            return $data->toArray();
        }
    }
}


if (!function_exists('SprintfArray')) {
    function SprintfArray($string, $array)
    {
        $keys    = array_keys($array);
        $keysmap = array_flip($keys);
        $values  = array_values($array);
        while (preg_match('/%\(([a-zA-Z0-9_ -]+)\)/', $string, $m))
        {
            if (!isset($keysmap[$m[1]]))
            {
                return false;
            }
            $string = str_replace($m[0], '%' . ($keysmap[$m[1]] + 1) . '$', $string);
        }

        array_unshift($values, $string);
        return call_user_func_array('sprintf', $values);
    }
}



