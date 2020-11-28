<?php
/**
 * @Author: Ali2vu <751815097@qq.com>
 * @Date: 2019-12-18 15:13:34
 * @LastEditors: Ali2vu
 * @LastEditTime: 2019-12-21 18:02:17
 */

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

$formatter = [
    'class' => \Monolog\Formatter\JsonFormatter::class,
    'constructor' => [
        'format' => null,
        'dateFormat' => null,
        'allowInlineLineBreaks' => true,
    ],
];

return [
    'default' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/hyperf.log',
                'level' => Monolog\Logger::INFO
            ],
        ],
        'formatter' => $formatter,
    ],
];
