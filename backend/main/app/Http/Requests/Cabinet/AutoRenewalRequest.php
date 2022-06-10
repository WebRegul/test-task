<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ImagesSectionSetRequest
 * @package App\Http\Requests\Cabinet
 */
class AutoRenewalRequest extends FormRequest
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
                  'auto' => [
                        'required',
                        Rule::in(['true', 'false', true, false])
                  ],

            ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
                  'auto.required' => 'не указан auto-renewal',
                  'auto.in' => 'auto-renewal не boolean',
            ];
    }
}
