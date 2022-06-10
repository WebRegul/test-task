<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;
use App\Models\UserContact;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Support\Arr;

/**
 * Class ContactUpdateRequest
 * @package App\Http\Requests\Cabinet
 */
class ContactUpdateRequest extends FormRequest
{
    /**
     * ContactUpdateRequest constructor.
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     */
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        request()->request->remove('contact_id');
        request()->merge(['contact_id' => request()->route('id')]);
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

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
            'contact_id' => [
                'bail',
                'required',
                'string',
                'exists:user_contacts,id',
            ],
            'value' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $userContact = UserContact::where('id', request('contact_id'))
                        ->with('type')
                        ->first();
                    $type = Arr::get($userContact, 'type.name');
                    switch ($type) {
                        case 'phone':
                            if (!preg_match('/^7\d{10}$/', $value)) {
                                $fail('телефон должен состоять из 11 цифр и начинаться с 7');
                            }
                            break;
                        case 'email':
                            $validator = new EmailValidator();
                            if (!$validator->isValid($value, new RFCValidation())) {
                                $fail('указан некорректный адрес электронной почты');
                            }
                            break;
                            // case 'instagram':
                            //     if (!preg_match('/^[a-z0-9\.\_\-]+$/i', $value)) {
                            //         $fail('указан некорректный логин в инстаграме');
                            //     }
                            //     break;
                            // case 'facebook':
                            //     if (!preg_match('/^[a-z0-9\.\_\-]+$/i', $value)) {
                            //         $fail('указан некорректный логин в фейсбуке');
                            //     }
                            //     break;
                            // case 'vkontakte':
                            //     if (!preg_match('/^[a-z0-9\.\_\-]+$/i', $value)) {
                            //         $fail('указан некорректный логин во вконтакте');
                            //     }
                            //     break;
                        default:
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
            'contact_id.required' => 'не указан id контакта',
            'contact_id.string' => 'id контакта не строка',
            'contact_id.exists' => 'указанного id контакта :input не существует',
            'value.required' => 'содержание контакта должно быть заполнено',
            'value.string' => 'содержание контакта должно быть строкой',
        ];
    }
}
