<?php

namespace App\Http\Requests\Images;

use Anik\Form\FormRequest;

/**
 * Class ImagesSectionSetRequest
 * @package App\Http\Requests\Cabinet
 */
class RevertImagesRequest extends FormRequest
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
                  'revert_id' => [
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
                  'revert_id.required' => 'не указан revert_id ',
                  'revert_id.string' => 'revert_id не строка',
            ];
    }
}
