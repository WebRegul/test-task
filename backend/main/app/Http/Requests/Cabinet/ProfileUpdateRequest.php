<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;
use App\Facades\Member;
use App\Models\Profile;

/**
 * Class ProfileUpdateRequest
 * @package App\Http\Requests\Cabinet
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'normal_slug',
                function ($attribute, $value, $fail) {
                    if (is_null($value) || empty($value)) {
                        return true;
                    }

                    $profile = Profile::query()
                        ->where('name', $value)
                        ->where('id', '!=', Member::get('profile.id'))
                        ->first();

                    if (!empty($profile)) {
                        $fail('Такой адрес уже занят');
                    }
                },

            ],
            'name' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (is_null($value) || empty($value)) {
                        return true;
                    }

                    $value = strval($value);
                    if (strlen($value) < 2) {
                        $fail('имя должно состоять минимум из 2 символов');
                    }
                    if (!preg_match('/^[a-zа-яё\-]+$/ui', $value)) {
                        $fail('имя должно содержать только буквы и тире');
                    }
                },
            ],
            'middle_name' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (is_null($value) || empty($value)) {
                        return true;
                    }

                    $value = strval($value);
                    if (strlen($value) < 2) {
                        $fail('отчество должно состоять минимум из 2 символов');
                    }
                    if (!preg_match('/^[a-zа-яё\-]+$/ui', $value)) {
                        $fail('отчество должно содержать только буквы и тире');
                    }
                },
            ],
            'surname' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (is_null($value) || empty($value)) {
                        return true;
                    }

                    $value = strval($value);
                    if (strlen($value) < 2) {
                        $fail('фамилия должна состоять минимум из 2 символов');
                    }
                    if (!preg_match('/^[a-zа-яё\-]+$/ui', $value)) {
                        $fail('фамилия должна содержать только буквы и тире');
                    }
                },
            ],
            'gender' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (is_null($value) || empty($value)) {
                        return true;
                    }

                    if ($value !== strval($value)) {
                        $fail('пол должен быть строкой');
                    }
                },
            ],
            'birthday_at' => [
                'sometimes',
                'real_date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Введите ссылку на профиль',
            'name.normal_slug' => 'ссылка на профиль может содержать только латиницу, цифры, знаки тире и подчеркивания',
            'birthday_at.real_date' => 'день рождения не является настоящей датой',
        ];
    }
}
