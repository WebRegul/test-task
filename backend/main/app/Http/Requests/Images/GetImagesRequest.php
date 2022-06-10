<?php

namespace App\Http\Requests\Images;

use Anik\Form\FormRequest;

/**
 * Class SaveImagesRequest
 * @package App\Http\Requests\Images
 */
class GetImagesRequest extends FormRequest
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
                  'entity_id' => [
                        'string', 'required'
                  ],
                  'entity_type' => [
                        'string', 'required'
                  ],
                  'section_id' => [
                        'string'
                  ],
            ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
                  'entity_id.string' => 'id сущности не строка',
                  'entity_id.required' => 'id сущности не передан',
                  'entity_type.string' => 'тип сущности не строка',
                  'section_id.string' => 'id секции не строка',
                  'entity_type.required' => 'тип сущности не передан',
            ];
    }
}
