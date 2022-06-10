<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;

/**
 * Class ImagesUpdateOrderRequest
 * @package App\Http\Requests\Cabinet
 */
class ImagesUpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'images' => [
                'required',
                'array',
                'real_images'
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'images.required' => 'не переданы идентификаторы изображений',
            'images.array' => 'идентификаторы изображений должны быть переданы в простом массиве',
            'images.real_images' => 'некоторых или всех изображений изображений не существует',
        ];
    }
}
