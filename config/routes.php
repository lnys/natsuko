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
use Hyperf\HttpServer\Router\Router;

Router::get('/', function () {
    return '404';
});

Router::addRoute(['POST'], '/service', 'App\Controller\ServiceController@add');

Router::get('/favicon.ico', function () {
    return '';
});
