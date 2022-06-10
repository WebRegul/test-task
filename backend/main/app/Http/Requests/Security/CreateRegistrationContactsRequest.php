<?php

namespace App\Http\Requests\Security;

use Anik\Form\FormRequest;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

/**
 * Class CreateRegistrationContactsRequest
 * @package App\Http\Requests\Security
 */
class CreateRegistrationContactsRequest extends FormRequest
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
            'email' => [
                'required',
                function ($attribute, $value, $fail) {
                    $validator = new EmailValidator();
                    if (!$validator->isValid($value, new RFCValidation())) {
                        $fail('указан некорректный адрес электронной почты');
                    }
                }
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'не введен номер телефона',
            'phone.regex' => 'номер телефона должен начинаться с 7',
            'phone.phone' => 'номер телефона не соответствует формату телефонов РФ',
            'email.required' => 'не введен адрес электронной почты',
        ];
    }
}
