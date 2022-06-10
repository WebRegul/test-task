<?php

namespace App\Http\Requests\Images;

use Anik\Form\FormRequest;

/**
 * Class SaveImagesRequest
 * @package App\Http\Requests\Images
 */
class SaveImagesRequest extends FormRequest
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
                        'string',
                        'exists:galleries,id'
                  ],
                  'section_id' => [
                        'bail',
                        'string',
                        'exists:gallery_sections,id'
                  ],
                  'images' => [
                        'string', 'required'
                  ],
                  'entity_id' => [
                        'string', 'required'
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
                  'gallery_id.string' => 'id галереи не строка',
                  'gallery_id.exists' => 'галереи с указанным id :input не существует',
                  'section_id.string' => 'id секции не строка',
                  'section_id.exists' => 'секции с указанным id :input не существует',
                  'images.string' => 'images не строка',
                  'images.required' => 'images не передан',
                  'entity_id.string' => 'id сущности не строка',
                  'entity_id.required' => 'id сущности не передан',
                  'entity_type.string' => 'тип сущности не строка',
                  'entity_type.required' => 'тип сущности не передан',
            ];
    }
}
