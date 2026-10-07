<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlackBelt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'rank',
        'promoted_on',
        'branch',
        'biography',
        'photo_path',
        'display_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'promoted_on' => 'date',
            'display_order' => 'integer',
            'status' => AccountStatus::class,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', AccountStatus::Active)->orderBy('display_order');
    }
}
