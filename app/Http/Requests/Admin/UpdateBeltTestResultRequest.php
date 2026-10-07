<?php

namespace App\Http\Requests\Admin;

use App\Enums\TestResultOutcome;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBeltTestResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('result')) ?? false;
    }

    public function rules(): array
    {
        return [
            'outcome' => ['required', Rule::enum(TestResultOutcome::class)],
            'score' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'result_date' => ['nullable', 'date'],
            'issue_certificate' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'issue_certificate' => $this->boolean('issue_certificate'),
        ]);
    }
}
