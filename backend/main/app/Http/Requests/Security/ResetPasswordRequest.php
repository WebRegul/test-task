<?php

namespace App\Http\Requests\Security;

use Anik\Form\FormRequest;

/**
 * Class PreregistrationRequest
 * @package App\Http\Requests\Security
 */
class ResetPasswordRequest extends FormRequest
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
                  'phone' => [
                        'required',
                        'regex:/^7.*$/',
                        'phone:RU',
                  ],

            ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
                  'phone.required' => 'Пожалуйста укажите ваш номер телефона',
                  'phone.regex' => 'Номер телефона должен начинаться с 7',
                  'phone.phone' => 'Номер телефона не соответствует формату телефонов РФ',
            ];
    }
}
