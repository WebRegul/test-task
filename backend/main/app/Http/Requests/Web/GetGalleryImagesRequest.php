<?php

namespace App\Http\Requests\Web;

use Anik\Form\FormRequest;

/**
 * Class GetGalleryImagesRequest
 * @package App\Http\Requests\Web
 */
class GetGalleryImagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'section_id' => [
                'sometimes',
                'string',
                'exists:gallery_sections,id',
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'section_id.string' => 'id секции не строка',
            'section_id.exists' => 'секции с указанным id :input не существует',
        ];
    }
}
