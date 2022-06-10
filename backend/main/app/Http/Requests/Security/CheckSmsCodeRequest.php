<?php

namespace App\Http\Requests\Security;

use Anik\Form\FormRequest;

/**
 * Class AuthVerifyByCodeRequest
 * @package App\Http\Requests\Security
 */
class CheckSmsCodeRequest extends FormRequest
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
                  'code' => [
                        'required',
                        'string',
                        'digits:4'
                  ],
                  'reset_id' => [
                        'required',
                        'string'
                  ],
                  'uid' => [
                        'required',
                        'string'
                  ]
            ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
                  'code.required' => 'не введен код верификации',
                  'code.string' => 'код верификации должен быть строкой',
                  'code.digits' => 'код верификации должен состоять из :digits цифр',
                  'reset_id.required' => 'не введен reset_id',
                  'reset_id.string' => 'reset_id должен быть строкой',
                  'uid.required' => 'не введен reset_id',
                  'uid.string' => 'reset_id должен быть строкой',
            ];
    }
}
