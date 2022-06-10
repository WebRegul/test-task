<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;

/**
 * Class ImagesSectionSetRequest
 * @package App\Http\Requests\Cabinet
 */
class ImagesSectionSetRequest extends FormRequest
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
            'gallery_id' => [
                'bail',
                'required',
                'string',
                'exists:galleries,id'
            ],
            'section_id' => [
                'bail',
                'required',
                'string',
                'exists:gallery_sections,id'
            ],
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
            'gallery_id.required' => 'не указан id галереи',
            'gallery_id.string' => 'id галереи не строка',
            'gallery_id.exists' => 'галереи с указанным id :input не существует',
            'section_id.required' => 'не указан id секции',
            'section_id.string' => 'id секции не строка',
            'section_id.exists' => 'секции с указанным id :input не существует',
            'images.required' => 'не переданы идентификаторы изображений',
            'images.array' => 'идентификаторы изображений должны быть переданы в простом массиве',
            'images.real_images' => 'некоторых или всех изображений изображений не существует',
        ];
    }
}
