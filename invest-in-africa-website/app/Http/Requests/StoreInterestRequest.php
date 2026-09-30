<?php

namespace App\Http\Requests;

use App\Enums\InvestorType;
use App\Support\Countries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Expression of interest (§6.2). */
class StoreInterestRequest extends FormRequest
{
    protected $errorBag = 'interest';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'investor_type' => ['required', Rule::enum(InvestorType::class)],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:32', 'regex:/^\+?[0-9 ().-]{6,30}$/'],
            'country' => ['required', Rule::in(array_keys(Countries::all()))],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:10000000000'],
            'message' => ['nullable', 'string', 'max:3000'],
            'consent' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return trans('forms.attributes');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => $this->filled('amount') ? preg_replace('/[^0-9.]/', '', (string) $this->input('amount')) : null,
        ]);
    }
}
