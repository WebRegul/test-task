<?php

namespace App\Http\Requests\Security;

use Anik\Form\FormRequest;

/**
 * Class RegistrationByUidRequest
 * @package App\Http\Requests\Security
 */
class RegistrationByUidRequest extends FormRequest
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
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'uid.required' => 'не введен uid',
            'uid.string' => 'uid должен быть строкой',
        ];
    }
}
