<?php

namespace App\Models;

use App\Enums\BeltHistorySource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StudentBeltHistory extends Model
{
    protected $fillable = [
        'student_id',
        'from_belt_id',
        'to_belt_id',
        'source',
        'source_ref_type',
        'source_ref_id',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'source' => BeltHistorySource::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function fromBelt(): BelongsTo
    {
        return $this->belongsTo(Belt::class, 'from_belt_id');
    }

    public function toBelt(): BelongsTo
    {
        return $this->belongsTo(Belt::class, 'to_belt_id');
    }

    public function sourceRef(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
