<?php

namespace App\Http\Requests\Cabinet;

use Anik\Form\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ProfileUpdateRequest
 * @package App\Http\Requests\Cabinet
 */
class ProfileTariffUpdateRequest extends FormRequest
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
                  'tariff_id' => [
                        'required',
                        'string',
                        Rule::exists('tariffs', 'id')->where(function ($query) {
                            $query->where('status', 1);
                        }),
                  ],
                  'period' => [
                        Rule::in(['month', 'year']),
                        'required'
                  ],

            ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
                  'tariff_id.required' => 'не указан id тарифа',
                  'tariff_id.string' => 'тарифа id не строка',
                  'tariff_id.exists' => 'тарифа с указанным id :input не существует',
                  'period.in' => 'период должен быть: :values',
                  'period.required' => 'не указан period',
            ];
    }
}
