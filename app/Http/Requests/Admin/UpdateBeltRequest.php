<?php

namespace App\Http\Requests\Admin;

use App\Enums\AccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBeltRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('belt')) ?? false;
    }

    public function rules(): array
    {
        $beltId = $this->route('belt')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'rank_order' => [
                'required',
                'integer',
                'min:1',
                'max:999',
                Rule::unique('belts', 'rank_order')->ignore($beltId),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::enum(AccountStatus::class)],
        ];
    }
}
