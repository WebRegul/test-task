<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;

/**
 * Class GetGalleryFullInfoRequest
 * @package App\Http\Requests\Cabinet
 */
class GetGalleryFullInfoRequest extends FormRequest
{
    /**
     * GetGalleryFullInfoRequest constructor.
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
        request()->request->remove('gallery_id');
        request()->merge(['gallery_id' => request()->route('id')]);
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
            'gallery_id' => [
                'bail',
                'required',
                'string',
                'exists:galleries,id',
            ],
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
        ];
    }
}
