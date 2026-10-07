<?php

namespace App\Http\Requests\Admin;

use App\Enums\BeltTestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBeltTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('belt_test')) ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('belt_test')?->id;

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('belt_tests', 'slug')->ignore($id)],
            'target_belt_id' => ['required', 'exists:belts,id'],
            'test_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:8'],
            'end_time' => ['nullable', 'string', 'max:8'],
            'application_opens_at' => ['nullable', 'date'],
            'application_closes_at' => ['nullable', 'date'],
            'fee_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'instructions' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::enum(BeltTestStatus::class)],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ];
    }
}
