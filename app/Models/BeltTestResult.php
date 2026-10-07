<?php

namespace App\Models;

use App\Enums\TestResultOutcome;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeltTestResult extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'belt_test_application_id',
        'belt_test_id',
        'student_id',
        'outcome',
        'score',
        'admin_notes',
        'result_date',
        'belt_updated',
        'recorded_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'outcome' => TestResultOutcome::class,
            'score' => 'decimal:2',
            'result_date' => 'date',
            'belt_updated' => 'boolean',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(BeltTestApplication::class, 'belt_test_application_id');
    }

    public function beltTest(): BelongsTo
    {
        return $this->belongsTo(BeltTest::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function isPassed(): bool
    {
        return $this->outcome === TestResultOutcome::Passed;
    }
}
