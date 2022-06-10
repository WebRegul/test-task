<?php

namespace App\Http\Requests\Images;

use Anik\Form\FormRequest;

/**
 * Class SaveImagesRequest
 * @package App\Http\Requests\Images
 */
class DropImagesRequest extends FormRequest
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
                  'ids' => [
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
                  'ids.required' => 'не переданы идентификаторы изображений',
                  'ids.array' => 'идентификаторы изображений должны быть переданы в простом массиве',
                  'ids.real_images' => 'некоторых или всех изображений изображений не существует',
            ];
    }
}
