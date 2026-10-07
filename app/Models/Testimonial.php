<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_name',
        'author_title',
        'content',
        'photo_path',
        'rating',
        'display_order',
        'status',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'display_order' => 'integer',
            'status' => AccountStatus::class,
            'is_approved' => 'boolean',
        ];
    }

    public function scopeApproved($query)
    {
        return $query
            ->where('status', AccountStatus::Active)
            ->where('is_approved', true)
            ->orderBy('display_order');
    }
}
