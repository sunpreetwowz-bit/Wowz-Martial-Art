<?php

namespace App\Http\Requests\Student;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitBeltTestApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $beltTest = $this->route('beltTest') ?? $this->route('belt_test');

        return $this->user()?->can('apply', $beltTest) ?? false;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'emergency_contact' => ['nullable', 'string', 'max:150'],
            'acknowledge' => ['accepted'],
        ];
    }
}
