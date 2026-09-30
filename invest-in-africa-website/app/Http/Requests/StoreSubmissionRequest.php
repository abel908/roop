<?php

namespace App\Http\Requests;

use App\Enums\FundingType;
use App\Enums\ProjectStage;
use App\Support\Countries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Submit a Project — server-side validation of the three steps (§6.3). */
class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $uploads = config('site.uploads');
        $countries = array_keys(Countries::all());

        return [
            // Step 1
            'full_name' => ['required', 'string', 'max:120'],
            'organization' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['required', 'string', 'max:32', 'regex:/^\+?[0-9 ().-]{6,30}$/'],
            'country' => ['required', Rule::in($countries)],
            'position' => ['required', 'string', 'max:120'],
            // Step 2
            'project_name' => ['required', 'string', 'max:160'],
            'project_country' => ['required', Rule::in($countries)],
            'sector_id' => ['required', 'integer', Rule::exists('sectors', 'id')->where('is_active', true)],
            'description' => ['required', 'string', 'min:50', 'max:'.$uploads['description_max']],
            'stage' => ['required', Rule::enum(ProjectStage::class)],
            'investment_amount' => ['required', 'numeric', 'min:1000', 'max:10000000000'],
            'currency' => ['required', Rule::in(config('site.currencies'))],
            'funding_type' => ['required', Rule::enum(FundingType::class)],
            'timeline' => ['required', 'string', 'max:160'],
            // Step 3
            'documents' => ['nullable', 'array', 'max:'.$uploads['max_files']],
            'documents.*' => [
                'file',
                'max:'.$uploads['max_kb'],
                'extensions:'.implode(',', $uploads['extensions']),
                'mimetypes:'.implode(',', $uploads['mimetypes']),
            ],
            'consent' => ['accepted'],
            'certify' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return trans('forms.attributes');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'investment_amount' => $this->filled('investment_amount')
                ? preg_replace('/[^0-9.]/', '', str_replace(',', '.', (string) $this->input('investment_amount')))
                : null,
        ]);
    }
}
