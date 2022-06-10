<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;

/**
 * Class ContactDeleteRequest
 * @package App\Http\Requests\Cabinet
 */
class ContactDeleteRequest extends FormRequest
{
    /**
     * ContactDeleteRequest constructor.
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
        request()->request->remove('contact_id');
        request()->merge(['contact_id' => request()->route('id')]);
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
            'contact_id' => [
                'bail',
                'required',
                'string',
                'exists:user_contacts,id',
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'contact_id.required' => 'не указан id контакта',
            'contact_id.string' => 'id контакта не строка',
            'contact_id.exists' => 'указанного id контакта :input не существует',
        ];
    }
}
