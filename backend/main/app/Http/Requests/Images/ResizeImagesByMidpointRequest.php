<?php

namespace App\Http\Requests\Images;

use Anik\Form\FormRequest;

/**
 * Class SaveImagesRequest
 * @package App\Http\Requests\Images
 */
class ResizeImagesByMidpointRequest extends FormRequest
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
            'width' => [
                'required',
                'integer',
            ],
            'height' => [
                'required',
                'integer',
            ],
            'coordX' => [
                'required',
                'integer',
            ],
            'coordY' => [
                'required',
                'integer',
            ],
            'position' => [
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
            'width.required' => 'не задана ширина фокусного изображения',
            'width.integer' => 'ширина фокусного изображения не число',
            'height.required' => 'не задана высота фокусного изображения',
            'height.integer' => 'высота фокусного изображения не число',
            'coordX.required' => 'не задана фокусная координата coordX',
            'coordX.integer' => 'фокусная координата coordX не число',
            'coordY.required' => 'не задана фокусная координата coordY',
            'coordY.integer' => 'фокусная координата coordY не число',
            'position.required' => 'не задана фокусная позиция',
            'position.string' => 'позиция не строка',
        ];
    }
}
