<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Kernel\Http\Response;
use Hyperf\Contract\ContainerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller as AnnotationController;

/**
 * @AnnotationController()
 */
class Controller extends AbstractController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Controller constructor.
     * @param  ContainerInterface  $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->get(RequestInterface::class);
        $this->response = $container->get(Response::class);
    }

    /**
     * 魔术方法
     * @author Ali2vu <751815097@qq.com>
     * @param $name
     * @return int
     */
    public function __get($name)
    {
        /**
         * 当前登录用户「如果存在」
         */
        if ($name === 'app') {
            return $this->request->getAttribute('app');
        }

        if ($name = 'version') {
            return config('version');
        }
    }

    /**
     * 返回成功JSON信息
     * @author Ali2vu <751815097@qq.com>
     * @param  array  $data
     * @param  int  $code
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function bizSuccess($info = []): \Psr\Http\Message\ResponseInterface
    {
        $code    = "0000";
        $message = "SUCCESS";

        return $this->response->json([
            'code'    => $code,
            'message' => $message,
            'data'    => $info,
        ])->withStatus(200);
    }

    /**
     * 返回失败JSON信息
     * @author Ali2vu <751815097@qq.com>
     * @param  array  $data
     * @param  int  $code
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function bizError(int $code, $message = null): \Psr\Http\Message\ResponseInterface
    {
        return $this->response->json([
            'code'    => $code,
            'message' => $message
        ])->withStatus(200);
    }

    /**
     * 返回业务JSON信息
     * @author Ali2vu <751815097@qq.com>
     * @param  array  $data
     * @param  int  $code
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function bizJson(array $info = []): \Psr\Http\Message\ResponseInterface
    {
        $code    = $info['code'] ?? "0000";
        $message = $info['message'] ?? "SUCCESS";
        $data    = $info['data'] ?? [];

        return $this->response->json([
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ])->withStatus(200);
    }

    /**
     * 返回JSON信息
     * @author Ali2vu <751815097@qq.com>
     * @param  array  $data
     * @param  int  $code
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function json($data = [], int $code = 200): \Psr\Http\Message\ResponseInterface
    {
        $response = $this->response->json($data)->withStatus($code);
        return $response;
    }

    /**
     * 返回raw
     * @author Ali2vu <751815097@qq.com>
     * @param $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function raw($data): \Psr\Http\Message\ResponseInterface
    {
        return $this->response->raw($data);
    }

    public function cleanCache()
    {
        cache()->clear();
    }
}
