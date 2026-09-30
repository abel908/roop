<?php

namespace App\Http\Requests;

use App\Enums\ContactSubject;
use App\Support\Countries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Contact Us form (§5.6). */
class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9 ().-]{6,30}$/'],
            'country' => ['nullable', Rule::in(array_keys(Countries::all()))],
            'subject' => ['required', Rule::enum(ContactSubject::class)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'consent' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return trans('forms.attributes');
    }
}
