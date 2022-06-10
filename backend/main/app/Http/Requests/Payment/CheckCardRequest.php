<?php

namespace App\Http\Requests\Payment;

use Anik\Form\FormRequest;
use App\Models\UserContact;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;

/**
 * Class CheckCardRequest
 * @package App\Http\Requests\Cabinet
 */
class CheckCardRequest extends FormRequest
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
        request()->request->remove('payment_id');
        request()->merge(['payment_id' => request()->route('payment_id')]);
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
                  'payment_id' => [
                        'string',
                        'exists:payments,id',
                  ],
            ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
                  'payment_id.string' => 'id платежа не строка',
                  'payment_id.exists' => 'указанного id платежа :input не существует',
            ];
    }
}
