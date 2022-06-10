<?php

namespace App\Http\Requests\Security;

use Anik\Form\FormRequest;

/**
 * Class AuthRepeatSendCodeRequest
 * @package App\Http\Requests\Security
 */
class AuthRepeatSendCodeRequest extends FormRequest
{
    /**
     * AuthRepeatSendCodeRequest constructor.
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
        request()->request->remove('user_id');
        request()->merge(['user_id' => request()->route('user_id')]);
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

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
            'user_id' => [
                'bail',
                'required',
                'string',
                'exists:users,id'
            ]
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'не указан id пользователя',
            'user_id.string' => 'id пользователя не строка',
            'user_id.exists' => 'пользователя с указанным id :input не существует',
        ];
    }
}
