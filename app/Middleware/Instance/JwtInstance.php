<?php

declare(strict_types=1);

namespace App\Middleware\Instance;

use App\Service\AppService;
use App\Model\OpenapiConfig;
use Phper666\JwtAuth\Jwt;
use Hyperf\Utils\Traits\StaticInstance;

/**
 * @Annotation
 *
 * Class JwtInstance
 * @package App\Middleware\Instance
 */
class JwtInstance
{
    use StaticInstance;
    /**
     * @var int
     */
    public $id;
    /**
     * @var App
     */
    public $app;

    public function encode(App $app)
    {
        $this->id = $app->id;
        return di()->get(Jwt::class)->getToken(['id' => $app->id]);
    }

    public function decode()
    : self {
        try {
            $decode = di()->get(Jwt::class)->getParserData();
        } catch (\Throwable $exception) {
            return $this;
        }

        if ($id = null ?? (int) $decode['id']) {
            $id = (string) $id;
            $this->id = $id;
            $this->app = di()->get(AppService::class)->getAppById($id);
        }

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getApp()
    {
        if ($this->app === null && $this->id) {
            $this->app = di()->get(AppService::class)->getAppById($this->id);
        }
        return $this->app;
    }
}
