<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;
use App\Models\User;

/**
 * Class AuthLoginRequest
 * @package App\Http\Requests\Security
 */
class ChangePhoneRequest extends FormRequest
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
                        function ($attribute, $value, $fail) {
                            $gallery = User::query()
                                    ->where('login', $value)
                                    ->first();

                            if (!empty($gallery)) {
                                $fail('такой логин уже занят');
                            }
                        },
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
            ];
    }
}
