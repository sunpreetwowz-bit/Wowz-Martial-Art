<?php

namespace App\Http\Requests\Admin;

use App\Enums\TestResultOutcome;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBeltTestResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\BeltTestResult::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'belt_test_application_id' => ['required', 'exists:belt_test_applications,id'],
            'outcome' => ['required', Rule::enum(TestResultOutcome::class)],
            'score' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'result_date' => ['nullable', 'date'],
            'issue_certificate' => ['sometimes', 'boolean'],
            'authorized_by_name' => ['nullable', 'string', 'max:150'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'issue_certificate' => $this->boolean('issue_certificate'),
        ]);
    }
}
