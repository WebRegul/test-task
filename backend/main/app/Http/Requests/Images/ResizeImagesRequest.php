<?php

namespace App\Http\Requests\Images;

use Anik\Form\FormRequest;

/**
 * Class SaveImagesRequest
 * @package App\Http\Requests\Images
 */
class ResizeImagesRequest extends FormRequest
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
                        'array', 'required'
                  ],
                  'entity_type' => [
                        'string', 'required'
                  ],
            ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
                  'images.array' => 'images не массив',
                  'images.required' => 'images не передан',
                  'entity_type.string' => 'тип сущности не строка',
                  'entity_type.required' => 'тип сущности не передан',
            ];
    }
}
