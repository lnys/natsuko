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
 * 权限控制
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class AclMiddleware extends AbstractAnnotation implements MiddlewareInterface
{
    protected $response;
    protected $request;

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
            $app['openapi_server'] = $app['openapi_server'] ?? [];
            $serviceList = json_decode($app['openapi_server'], true);
            $service = $this->request->input("service");
            if (!in_array($service, $serviceList)) {
                $message = sprintf("SERVICE: %s 不允许访问，请联系客服", $service);
                R($message, "", true);
                throw new SysException($message, 401);
            }
        }

        return  $handler->handle($request);
    }
}
