<?php

/**
 * @Author: Ali2vu <751815097@qq.com>
 * @Date: 2019-12-20 22:05:59
 * @LastEditors: Ali2vu
 * @LastEditTime: 2019-12-25 11:32:54
 */

declare(strict_types=1);

use App\Task\GraylogTask;
use App\Service\Factory\GraylogFactory;
use Hyperf\Server\Exception\ServerException;
use Hyperf\Utils\ApplicationContext;

if (! function_exists('stop')) {
    function stop($string = '')
    {
        throw new ServerException($string);
    }
}

if (! function_exists('E')) {
    function E($name = '')
    {
        print_r($name);
        echo PHP_EOL;

        // LOG
        $strName = $name !== "" ? sprintf("\n%s", $name) : "";
        $fullStr = $strName;
        if ($fullStr) {
            ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get("E")->info($fullStr);
        }
    }
}

if (! function_exists('R')) {
    function R($array, $name = '', bool $log = false)
    {
        if ($array === "") {
            return;
        }
        echo PHP_EOL.date('Y-m-d H:i:s').PHP_EOL;
        if ($name) {
            echo $name . PHP_EOL;
        }
        print_r($array);
        echo PHP_EOL;

        // LOG
        $strName = $name !== "" ? sprintf("\n%s", $name) : "";
        $strData = $array !== "" ? sprintf("\n%s", print_r($array, true)) : "";
        $fullStr = $strName . $strData;
        if ($fullStr) {
            ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get("R")->info($fullStr);
            if ($log) {
                ApplicationContext::getContainer()->get(GraylogFactory::class)->store($fullStr, $fullStr);
            }
        }
    }
}

if (! function_exists('L')) {
    function L($array, $name = '')
    {
        // LOG
        $strName = $name !== "" ? sprintf("\n%s", $name) : "";
        if ($strName === "") {
            $strName = is_string($array) ? $array : json_encode($array, JSON_UNESCAPED_UNICODE);
        }
        $strData = $array !== "" ? sprintf("\n%s", json_encode($array,  JSON_UNESCAPED_UNICODE)) : "";
        $fullStr = $strName . $strData;
        if ($fullStr) {
            ApplicationContext::getContainer()->get(GraylogFactory::class)->store($strData, $strName);
        }
    }
}

if (! function_exists('D')) {
    function D($array, $name = '')
    {
        R($array, $name);
        stop(1);
    }
}

if (! function_exists('V')) {
    function V($array, $name = '')
    {
        if ($array === "") {
            return;
        }
        echo PHP_EOL.date('Y-m-d H:i:s').PHP_EOL;
        if ($name) {
            echo $name . PHP_EOL;
        }
        var_dump($array);
        echo PHP_EOL;

        // LOG
        $strName = $name !== "" ? sprintf("\n%s", $name) : "";
        $strData = $array !== "" ? sprintf("\n%s", print_r($array, true)) : "";
        $fullStr = $strName . $strData;
        if ($fullStr) {
            ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get("V")->info($fullStr);
        }
    }
}
