<?php
/**
 * @Author: Ali2vu <751815097@qq.com>
 * @Date: 2019-12-20 11:30:18
 * @LastEditors: Ali2vu
 * @LastEditTime: 2020-01-08 00:20:16
 */

declare(strict_types=1);

namespace App\Middleware;

use Phper666\JwtAuth\Jwt;
use App\Exception\SysException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\Common\Annotations\Annotation\Target;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;


/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class JwtAuthMiddleware extends AbstractAnnotation implements MiddlewareInterface
{
    protected $response;
    protected $prefix = 'Bearer';
    protected $jwt;

    public function __construct(HttpResponse $response, Jwt $jwt)
    {
        parent::__construct();
        $this->response = $response;
        $this->jwt = $jwt;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    : ResponseInterface {
        if (!$request->getAttribute('app')) {
            $isValidToken = false;
            $token = $request->getHeader('Authorization')[0] ?? '';
            $token = substr($token, 7);
            if ($token) {
                try {
                    if ($token !== '' && $this->jwt->checkToken()) {
                        $isValidToken = true;
                    }
                } catch (\Exception $e) {
                    throw new SysException("token已过期", 401);
                }
            } else {
                throw new SysException('请先登录', 401);
            }

            if ($isValidToken) {
                return $handler->handle($request);
            }

            throw new SysException("token无效", 401);
        }

        return  $handler->handle($request);
    }
}
