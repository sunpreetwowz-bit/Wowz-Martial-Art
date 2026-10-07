<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $fillable = [
        'key',
        'title',
        'content',
        'image_path',
        'display_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'status' => AccountStatus::class,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', AccountStatus::Active)->orderBy('display_order');
    }
}
