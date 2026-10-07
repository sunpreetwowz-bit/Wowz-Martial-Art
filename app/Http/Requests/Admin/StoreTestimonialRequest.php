<?php

namespace App\Http\Requests\Admin;

use App\Enums\AccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:150'],
            'author_title' => ['nullable', 'string', 'max:150'],
            'content' => ['required', 'string', 'max:5000'],
            'photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', Rule::enum(AccountStatus::class)],
            'is_approved' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_approved' => $this->boolean('is_approved'),
        ]);
    }
}
