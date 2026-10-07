<?php

namespace App\Http\Requests\Admin;

use App\Enums\AccountStatus;
use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('student')) ?? false;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $userId = $student?->user_id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'address' => ['nullable', 'string', 'max:2000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'joining_date' => ['nullable', 'date'],
            'profile_photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'current_belt_id' => ['nullable', 'exists:belts,id'],
            'primary_service_id' => ['nullable', 'exists:services,id'],
            'status' => ['required', Rule::enum(AccountStatus::class)],
            'notes' => ['nullable', 'string', 'max:5000'],
            'belt_change_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
