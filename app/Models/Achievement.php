<?php

namespace App\Models;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'student_id',
        'competition_name',
        'achievement_type',
        'position',
        'achieved_on',
        'image_path',
        'document_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'achieved_on' => 'date',
            'status' => PublishStatus::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', PublishStatus::Published);
    }
}
