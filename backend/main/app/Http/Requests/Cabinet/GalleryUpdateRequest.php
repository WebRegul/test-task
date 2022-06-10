<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;
use App\Facades\Member;
use App\Models\Gallery;
use App\Models\Image;
use Illuminate\Validation\Rule;

/**
 * Class GalleryUpdateRequest
 * @package App\Http\Requests\Cabinet
 */
class GalleryUpdateRequest extends FormRequest
{
    /**
     * GalleryUpdateRequest constructor.
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
        $values = ['false', 0, false];

        $checkKeys = ['is_secure', 'is_download_secure'];
        foreach ($checkKeys as $checkKey) {
            if (request()->has($checkKey)) {
                $flag = boolval(request($checkKey));
                request()->request->remove($checkKey);
                request()->merge([$checkKey => $flag]);
            } else {
                request()->merge([$checkKey => false]);
            }
        }

        request()->request->remove('gallery_id');
        request()->merge(['gallery_id' => request()->route('id')]);
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
            'gallery_id' => [
                'bail',
                'required',
                'string',
                'exists:galleries,id',
            ],
            'title' => [
                'filled',
                'regex:/^[\w\d\.\,\:\;\-\?\!\ ё]+$/ui',
            ],
            'shooting_at' => [
                'sometimes', 'date', 'date_format:d.m.Y', 'nullable'
            ],
            'name' => [
                'filled',
                'normal_slug',
                function ($attribute, $value, $fail) {
                    $gallery = Gallery::query()
                        ->where('user_id', Member::get('id'))
                        ->where('name', $value)
                        ->where('id', '!=', request('gallery_id'))
                        ->first();

                    if (!empty($gallery)) {
                        $fail('такой адрес галереи уже занят');
                    }
                },
            ],
            'template_options' => [
                'sometimes',
                'array',
            ],
            'cover_id' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (is_null($value) || empty($value)) {
                        return true;
                    }

                    if (empty(Image::query()->find($value))) {
                        $fail('обложки :input не существует');
                    }
                },
            ],
            'download_type' => [
                'sometimes',
                Rule::in(['all', 'only_web']),
            ],
            'password' => [
                Rule::requiredIf(function () {
                    return request('is_secure');
                }),
                // 'normal_password',
            ],
            'download_password' => [
                Rule::requiredIf(function () {
                    return request('is_download_secure');
                }),
                // 'normal_password',
            ]
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'gallery_id.required' => 'не указан id галереи',
            'gallery_id.string' => 'id галереи не строка',
            'gallery_id.exists' => 'галереи с указанным id :input не существует',
            'title.filled' => 'заголовок не должен быть пустым',
            'title.regex' => 'заголовок галереи должен содержать только буквы, цифры, пробелы и знаки препинания',
            'shooting_at.date_format' => 'Введите корректную дату',
            'shooting_at.date' => 'Введите корректную дату',
            'name.filled' => 'ссылка галереи не должна быть пустой',
            'name.normal_slug' => 'ссылка галереи может содержать только латиницу, цифры, знаки тире и подчеркивания',
            'name.unique' => 'ссылка галереи уже используется: используйте другое название',
            'template_options.array' => 'опции галереи должны быть массивом',
            'download_type.in' => 'тип загрузки должен быть: :values',
            'password.required' => 'для защищенной галереи заполнение пароля обязательно',
            'password.normal_password' => 'пароль должен состоять из'
                . ' минимум 6 символов и не должен включать русские буквы',
            'download_password.required' => 'для защищенного скачивания галереи'
                . ' заполнение пароля для скачивания обязательно',
            'download_password.normal_password' => 'пароль для скачивания должен состоять из'
                . ' минимум 6 символов и не должен включать русские буквы',
        ];
    }
}
