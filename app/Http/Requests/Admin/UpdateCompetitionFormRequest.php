<?php

namespace App\Http\Requests\Admin;

use App\Enums\AccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompetitionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('competition_form')) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220'],
            'description' => ['nullable', 'string', 'max:5000'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'google_form_url' => ['nullable', 'url', 'max:500'],
            'deadline_at' => ['nullable', 'date'],
            'status' => ['required', Rule::enum(AccountStatus::class)],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ];
    }
}
