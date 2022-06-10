<?php

namespace App\Http\Requests\Security;

use Anik\Form\FormRequest;

/**
 * Class AuthRegistrationRequest
 * @package App\Http\Requests\Security
 */
class AuthRegistrationRequest extends FormRequest
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
                'required',
                'regex:/^7.*$/',
                'phone:RU',
            ],
            'password' => [
                'required',
                'normal_password'
            ],
            'name' => [
                'required',
                'min:2',
                'regex:/^[a-zа-яё\-]+$/ui'
            ],
            'surname' => [
                'required',
                'min:2',
                'regex:/^[a-zа-яё\-]+$/ui'
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'login.required' => 'не введен номер телефона',
            'login.regex' => 'номер телефона должен начинаться с 7',
            'login.phone' => 'номер телефона не соответствует формату телефонов РФ',
            'password.required' => 'не введен пароль',
            'password.normal_password' => 'пароль должен состоять из'
                . ' минимум 6 символов и не должен включать русские буквы',
            'name.required' => 'не введено имя',
            'name.min' => 'имя должно состоять минимум из :min символов',
            'name.regex' => 'имя должно содержать только буквы и тире',
            'surname.required' => 'не введена фамилия',
            'surname.min' => 'фамилия должна состоять минимум из :min символов',
            'surname.regex' => 'фамилия должна содержать только буквы и тире',
        ];
    }
}
