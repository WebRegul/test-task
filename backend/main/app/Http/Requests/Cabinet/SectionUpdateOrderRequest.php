<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;

use Illuminate\Validation\Rule;

/**
 * Class SectionsListGetRequest
 * @package App\Http\Requests\Cabinet
 */
class SectionUpdateOrderRequest extends FormRequest
{
    /**
     * SectionsListGetRequest constructor.
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     */
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        request()->request->remove('section_id');
        request()->merge(['section_id' => request()->route('id')]);

        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

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
            'section_id' => [
                'bail',
                'required',
                'string',
                'exists:gallery_sections,id'
            ],
            'mode' => [
                'required',
                Rule::in(['manual', 'desc', 'asc', null])
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'mode.required' => 'не указан тип сортировки',
            'mode.*' => 'неизвестный тип сортировки',
        ];
    }
}
