<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Belt extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'color',
        'rank_order',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rank_order' => 'integer',
            'status' => AccountStatus::class,
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'current_belt_id');
    }

    public function beltTests(): HasMany
    {
        return $this->hasMany(BeltTest::class, 'target_belt_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', AccountStatus::Active);
    }
}
