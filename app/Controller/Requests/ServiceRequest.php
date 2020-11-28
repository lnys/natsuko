<?php

declare(strict_types=1);

namespace App\Controller\Requests;

use Hyperf\Validation\Request\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    : array
    {
        return [
            'service' => [
                'required',
                'string'
            ],
            'appKey' => [
                'required',
                'string'
            ],
            'version' => [
                'required',
                'string'
            ],
            'requestBody' => [
                'required',
                'string'
            ],
            'timestamp' => [
                'required',
                'string'
            ],
            'signature' => [
                'required',
                'string'
            ],
        ];
    }

    public function messages()
    : array
    {
        return [
            'service.required'   => 'service不能为空',
            'appKey.required'   => 'appKey不能为空',
            'version.required'   => 'version不能为空',
            'requestBody.required'   => 'requestBody不能为空',
            'timestamp.required'   => 'timestamp不能为空',
            'signature.required'   => 'signature不能为空',
        ];
    }
}
