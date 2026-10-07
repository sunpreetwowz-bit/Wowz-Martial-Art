<?php

namespace App\Http\Requests\Admin;

use App\Enums\AccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBeltRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Belt::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'rank_order' => ['required', 'integer', 'min:1', 'max:999', 'unique:belts,rank_order'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::enum(AccountStatus::class)],
        ];
    }
}
