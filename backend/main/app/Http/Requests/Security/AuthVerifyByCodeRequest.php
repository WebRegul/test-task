<?php

namespace App\Http\Requests\Security;

use Anik\Form\FormRequest;

/**
 * Class AuthVerifyByCodeRequest
 * @package App\Http\Requests\Security
 */
class AuthVerifyByCodeRequest extends FormRequest
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
            'user_id' => [
                'bail',
                'required',
                'string',
                'exists:users,id'
            ],
            'code' => [
                'required',
                'string',
                'digits:4'
            ],
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
            'code.required' => 'не введен код верификации',
            'code.string' => 'код верификации должен быть строкой',
            'code.digits' => 'код верификации должен состоять из :digits цифр',
        ];
    }
}
