<?php
/**
 * @Author: Ali2vu <751815097@qq.com>
 * @Date: 2019-12-21 00:25:07
 * @LastEditors: Ali2vu
 * @LastEditTime: 2019-12-21 13:49:15
 */

namespace App\Exception;

use App\Constants\ErrorCode;
use Hyperf\Server\Exception\ServerException;
use Throwable;

class ApiException extends ServerException
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message) || !$message) {
            $message = ErrorCode::getMessage($code);
        }

        parent::__construct($message, $code, $previous);
    }
}