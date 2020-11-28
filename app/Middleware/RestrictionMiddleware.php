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
use Hyperf\HttpServer\Contract\RequestInterface as HttpRequest;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

/**
 * IP白名单
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class RestrictionMiddleware extends AbstractAnnotation implements MiddlewareInterface
{
    protected $response;

    public function __construct(HttpRequest $request, HttpResponse $response)
    {
        parent::__construct();
        $this->request = $request;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    : ResponseInterface {
        $app = $request->getAttribute('app');
        if ($app) {
            $app['is_restriction'] = $app['is_restriction'] ?? 1;
            if ($app['is_restriction']) {
                $ipList = explode("," , $app['ip_whitelist']);
                if (!in_array(getip(), $ipList)) {
                    $message = sprintf("IP: %s 不允许访问", getip());
                    R($message, "", true);
                    throw new SysException($message, 401);
                }
            }
        }

        return  $handler->handle($request);
    }
}
