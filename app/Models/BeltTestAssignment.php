<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeltTestAssignment extends Model
{
    protected $fillable = [
        'belt_test_id',
        'student_id',
        'status',
        'assigned_by',
        'assigned_at',
        'revoked_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssignmentStatus::class,
            'assigned_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function beltTest(): BelongsTo
    {
        return $this->belongsTo(BeltTest::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isActive(): bool
    {
        return $this->status === AssignmentStatus::Assigned;
    }
}
