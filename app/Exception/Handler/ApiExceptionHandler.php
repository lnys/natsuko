<?php
/**
 * @Author: Ali2vu <751815097@qq.com>
 * @Date: 2019-12-21 00:24:19
 * @LastEditors: Ali2vu
 * @LastEditTime: 2019-12-21 01:36:13
 */

namespace App\Exception\Handler;

use App\Kernel\Http\Response;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use App\Exception\ApiException;
use Throwable;

class ApiExceptionHandler extends  ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $responses)
    {
        $response = di()->get(Response::class);
        // 判断被捕获到的异常是希望被捕获的异常
        if ($throwable instanceof ApiException) {
            // 格式化输出
            $data = [
                'code' => $throwable->getCode(),
                'message' => $throwable->getMessage(),
            ];

            // 格式化错误信息
            if (env('APP_ENV') === 'dev') {
                echo PHP_EOL;
                echo "> [EX] ". "--------------------------[报错指南]----------------------------" . date("Y-m-d H:i:s") . PHP_EOL;
                echo "> [EX] ". "异常消息：" . $throwable->getMessage() . PHP_EOL;
                echo "> [EX] ". "文件：" . $throwable->getFile() . PHP_EOL;
                echo "> [EX] ". "位置：" . $throwable->getLine() . "行" . PHP_EOL;
                foreach (explode('#', $throwable->getTraceAsString()) as $key => $value) {
                    echo "> [EX] ". '#'.$value;
                }
                // print_context($this->getContext());
                echo PHP_EOL . "> [EX] ". "--------------------------------------------------------------" . PHP_EOL . PHP_EOL;
            }

            // 阻止异常冒泡
            $this->stopPropagation();
            return $response->json($data);
        }

        // 交给下一个异常处理器
        return $responses;

        // 或者不做处理直接屏蔽异常
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     */
    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}