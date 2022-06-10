<?php

namespace App\Http\Requests\Security;

use Anik\Form\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class AuthLoginRequest
 * @package App\Http\Requests\Security
 */
class AuthLoginRequest extends FormRequest
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
            'login' => [
                'required_with:password',
                'regex:/^7.*$/',
                'phone:RU',
            ],
            'password' => [
                'required_with:login',
                // 'normal_password',
            ],
            'type' => [
                'required_with:uid',
                Rule::in([
                    'oauth',
                ]),
            ],
            'uid' => [
                'required_with:type',
                'string',
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'login.required_with' => 'не введен номер телефона',
            'login.regex' => 'номер телефона должен начинаться с 7',
            'login.phone' => 'номер телефона не соответствует формату телефонов РФ',
            'password.required_with' => 'не введен пароль',
            'password.normal_password' => 'пароль должен состоять из'
                . ' минимум 6 символов и не должен включать русские буквы',
            'type.required_with' => 'тип не должен быть пустым',
            'type.in' => 'значение типа может быть только :values',
            'uid.required_with' => 'uid не должен быть пустым',
            'uid.string' => 'uid должен быть строкой',
        ];
    }
}
