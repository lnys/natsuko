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

namespace App\Kernel\Http;

use App\Constants\ErrorCode;
use Hyperf\Utils\ApplicationContext;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class Response
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->response = $container->get(ResponseInterface::class);
    }

    /**
     * JSON数据返回
     * @author Ali2vu <751815097@qq.com>
     * @param $data
     * @return PsrResponseInterface
     */
    public function json($data)
    {
        $response = $this->response->json($data);

        if (env('APP_MESSAGE') === true) {
            // 获取请求，响应报文
            $request = ApplicationContext::getContainer()->get(RequestInterface::class);
            $executionTime = (microtime(true) - Context::get('request_start_time')) * 1000;
            $headers = $request->getHeaders();
            $serverParams = $request->getServerParams();
            $arguments = $request->all();
            $content = $response->getBody()->getContents();
            $queryString = '?' . ($serverParams['query_string'] ?? '');
            $requestMethod = $serverParams['request_method'] ?? '';
            $questUri = $serverParams['request_uri'] ?? '';

            echo PHP_EOL;
            echo "> [RS] ". "--------------------------[HTTP报文]----------------------------" . date("Y-m-d H:i:s") . PHP_EOL;
            echo "> [RS] ". "路由：" . $requestMethod . " " . $questUri . $queryString . PHP_EOL;
            foreach ($headers as $key => $value) {
                $value = is_array($value) ? implode(" ", $value) : '';
                echo "> [RS] ". ''."${key}: ${value}" . PHP_EOL;
            }
            echo "> [RS] ". "请求数据：" . json_encode($arguments, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            echo "> [RS] ". "响应数据：" . json_encode($content, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            echo "> [RS] ". "Time：" . $executionTime . "ms" . PHP_EOL;
            echo "> [RS] ". "--------------------------------------------------------------" . PHP_EOL . PHP_EOL;
        }

        return $response;
    }

    /**
     * RAW数据返回
     * @author Ali2vu <751815097@qq.com>
     * @param $data
     * @return PsrResponseInterface
     */
    public function raw($data)
    {
        $response = $this->response->raw($data);

        if (env('APP_MESSAGE') === true) {
            // 获取请求，响应报文
            $request = ApplicationContext::getContainer()->get(RequestInterface::class);
            $executionTime = (microtime(true) - Context::get('request_start_time')) * 1000;
            $headers = $request->getHeaders();
            $serverParams = $request->getServerParams();
            $arguments = $request->all();
            $content = $response->getBody()->getContents();
            $queryString = '?' . ($serverParams['query_string'] ?? '');
            $requestMethod = $serverParams['request_method'] ?? '';
            $questUri = $serverParams['request_uri'] ?? '';

            echo PHP_EOL;
            echo "> [RS] ". "--------------------------[HTTP报文]----------------------------" . date("Y-m-d H:i:s") . PHP_EOL;
            echo "> [RS] ". "路由：" . $requestMethod . " " . $questUri . $queryString . PHP_EOL;
            foreach ($headers as $key => $value) {
                $value = is_array($value) ? implode(" ", $value) : '';
                echo "> [RS] ". ''."${key}: ${value}" . PHP_EOL;
            }
            echo "> [RS] ". "请求数据：" . json_encode($arguments, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            echo "> [RS] ". "响应数据：" . json_encode($content, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            echo "> [RS] ". "Time：" . $executionTime . "ms" . PHP_EOL;
            echo "> [RS] ". "--------------------------------------------------------------" . PHP_EOL . PHP_EOL;
        }

        return $response;
    }

    /**
     * @param string $message
     * @param int $code
     * @return PsrResponseInterface
     */
    public function error($message = '', $code = ErrorCode::SERVER_ERROR)
    {
        return $this->json([
            'code' => $code,
            'msg' => $message,
        ]);
    }

    public function redirect($url, $status = 302)
    {
        return $this->response()
            ->withAddedHeader('Location', (string)$url)
            ->withStatus($status);
    }

    public function cookie(Cookie $cookie)
    {
        $response = $this->response()->withCookie($cookie);
        Context::set(PsrResponseInterface::class, $response);
        return $this;
    }

    /**
     * @return \Hyperf\HttpMessage\Server\Response
     */
    public function response()
    {
        return Context::get(PsrResponseInterface::class);
    }
}
