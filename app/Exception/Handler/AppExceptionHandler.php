<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Exception\Handler;

use App\Kernel\Http\Response;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response, $islog = true)
    {
        $response = di()->get(Response::class);
        // 格式化错误信息
        $message = PHP_EOL;
        $message .= "> [EX] ". "--------------------------[报错指南]----------------------------" . date("Y-m-d H:i:s") . PHP_EOL;
        $message .= "> [EX] ". "异常消息：" . $throwable->getMessage() . PHP_EOL;
        $message .= "> [EX] ". "文件：" . $throwable->getFile() . PHP_EOL;
        $message .= "> [EX] ". "位置：" . $throwable->getLine() . "行" . PHP_EOL;
        foreach (explode('#', $throwable->getTraceAsString()) as $key => $value) {
            $message .= "> [EX] ". '#'.$value;
        }
        // print_context($this->getContext());
        $message .= "> [EX] ". "--------------------------------------------------------------" . PHP_EOL;
        // $this->logger->error($throwable->getMessage() . PHP_EOL . $message);
        echo $message;

        return $response->json("服务出现异常，请联系管理员")->withStatus(500);
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
