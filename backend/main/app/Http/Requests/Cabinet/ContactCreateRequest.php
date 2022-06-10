<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

/**
 * Class ContactCreateRequest
 * @package App\Http\Requests\Cabinet
 */
class ContactCreateRequest extends FormRequest
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
            'type' => [
                'required',
                'string',
                'exists:user_contacts_types,name'
            ],
            'value' => [
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
            'type.required' => 'не указан тип контакта',
            'type.string' => 'тип контакта должен быть строкой',
            'type.exists' => 'указанного типа контактов :input не существует',
            'value.required' => 'значение контакта должно быть заполнено',
            'value.string' => 'значение контакта должно быть строкой',
        ];
    }
}
