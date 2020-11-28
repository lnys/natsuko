<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Kernel\Http\Response;
use App\Exception\ValidationException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $responses)
    {
        $response = di()->get(Response::class);
        $this->stopPropagation();
        /** @var \Hyperf\Validation\ValidationException $throwable */
        $body = $throwable->validator->errors()->first();
        // 格式化输出
        $data = [
            'code' => 400,
            'message' => $throwable->validator->errors()->first(),
        ];
        return $response->json($data)->withStatus(400);
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof \Hyperf\Validation\ValidationException;
    }
}
