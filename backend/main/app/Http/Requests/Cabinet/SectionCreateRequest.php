<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;

/**
 * Class SectionCreateRequest
 * @package App\Http\Requests\Cabinet
 */
class SectionCreateRequest extends FormRequest
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
            'title' => [
                'required',
                'regex:/^[\w\d\.\,\:\;\-\?\!\ ё]+$/ui'
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
            'title.required' => 'не введен заголовок секции',
            'title.regex' => 'заголовок секции должен содержать только буквы, цифры, пробелы и знаки препинания',
        ];
    }
}
