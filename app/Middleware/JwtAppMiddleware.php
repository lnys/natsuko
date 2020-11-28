<?php
declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Utils\Context;
use Phper666\JwtAuth\Jwt;
use Psr\Http\Message\ResponseInterface;
use App\Middleware\Instance\JwtInstance;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\Common\Annotations\Annotation\Target;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;


/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class JwtAppMiddleware extends AbstractAnnotation implements MiddlewareInterface
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

                }
            }

            if ($isValidToken) {
                $request = $request->withAttribute('app', JwtInstance::instance()->decode()->getApp());
                $request = Context::set(ServerRequestInterface::class, $request);
            } else {
                $request = $request->withAttribute('app', null);
                $request = Context::set(ServerRequestInterface::class, $request);
            }
        }

        return $handler->handle($request);
    }
}
