<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;
use App\Models\User;

/**
 * Class AuthLoginRequest
 * @package App\Http\Requests\Security
 */
class ChangePasswordVerifyRequest extends FormRequest
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
                  'password' => [
                        'required',
                        'normal_password'
                  ],
                  'reset_id' => [
                        'required',
                  ],
            ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
                  'password.required' => 'не введен пароль',
                  'password.normal_password' => 'пароль должен состоять из'
                        . ' минимум 6 символов и не должен включать русские буквы',
                  'reset_id.required' => 'не введен reset_id',
            ];
    }
}
