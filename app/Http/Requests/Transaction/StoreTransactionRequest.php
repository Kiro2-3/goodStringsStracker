<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'type'        => ['required', 'in:income,expense'],
            'category'    => ['required', 'string'],
            'entry_date'  => ['required', 'date'],
        ];
    }
}
