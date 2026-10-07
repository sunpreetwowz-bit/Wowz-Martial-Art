<?php

namespace App\Models;

use App\Enums\BeltTestStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BeltTest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'target_belt_id',
        'test_date',
        'start_time',
        'end_time',
        'application_opens_at',
        'application_closes_at',
        'fee_amount',
        'currency',
        'instructions',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'test_date' => 'date',
            'application_opens_at' => 'datetime',
            'application_closes_at' => 'datetime',
            'fee_amount' => 'decimal:2',
            'status' => BeltTestStatus::class,
        ];
    }

    public function targetBelt(): BelongsTo
    {
        return $this->belongsTo(Belt::class, 'target_belt_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(BeltTestAssignment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'belt_test_assignments')
            ->withPivot(['status', 'assigned_by', 'assigned_at', 'revoked_at', 'notes'])
            ->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(BeltTestApplication::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(BeltTestResult::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Effective application close time.
     * If application_closes_at is null, defaults to test start - 30 minutes.
     */
    public function effectiveApplicationClosesAt(): Carbon
    {
        if ($this->application_closes_at) {
            return $this->application_closes_at->copy();
        }

        $minutes = (int) config('academy.belt_test_application_close_minutes', 30);

        return Carbon::parse(
            $this->test_date->format('Y-m-d').' '.$this->start_time,
            config('app.timezone')
        )->subMinutes($minutes);
    }

    public function testStartsAt(): Carbon
    {
        return Carbon::parse(
            $this->test_date->format('Y-m-d').' '.$this->start_time,
            config('app.timezone')
        );
    }
}
