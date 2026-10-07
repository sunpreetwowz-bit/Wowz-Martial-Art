<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'designation',
        'biography',
        'photo_path',
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
