<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeltTestApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'belt_test_id',
        'student_id',
        'current_belt_id',
        'target_belt_id',
        'form_data',
        'status',
        'submitted_at',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'status' => ApplicationStatus::class,
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
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

    public function currentBelt(): BelongsTo
    {
        return $this->belongsTo(Belt::class, 'current_belt_id');
    }

    public function targetBelt(): BelongsTo
    {
        return $this->belongsTo(Belt::class, 'target_belt_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function result(): HasOne
    {
        return $this->hasOne(BeltTestResult::class);
    }
}
