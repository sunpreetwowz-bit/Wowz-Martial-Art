<?php

namespace App\Http\Requests\Admin;

use App\Enums\AccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAboutSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'content' => ['nullable', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', Rule::enum(AccountStatus::class)],
        ];
    }
}
