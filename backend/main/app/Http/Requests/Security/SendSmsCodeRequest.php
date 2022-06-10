<?php

namespace App\Http\Requests\Security;

use Anik\Form\FormRequest;

/**
 * Class AuthVerifyByCodeRequest
 * @package App\Http\Requests\Security
 */
class SendSmsCodeRequest extends FormRequest
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
                  'uid' => [
                        'required',
                        'string',
                  ]
            ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
                  'uid.required' => 'Не передан uid',
                  'uid.string' => 'uid не строка',
            ];
    }
}
