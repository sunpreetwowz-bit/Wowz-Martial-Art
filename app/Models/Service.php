<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
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

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'primary_service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', AccountStatus::Active)->orderBy('display_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
