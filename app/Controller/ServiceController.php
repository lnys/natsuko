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
namespace App\Controller;

use App\Controller\Requests\ServiceRequest;

class ServiceController extends Controller
{
    public function add(ServiceRequest $request)
    {
        $data = $request->validated();
        R($data, 'data');
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        R($method, "demo");

        return $this->bizSuccess([
            'method' => $method,
            'message' => "Hello {$user}.",
        ]);
    }

    public function edit()
    {

    }
}
