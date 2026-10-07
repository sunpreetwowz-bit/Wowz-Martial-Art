<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'student_code',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'joining_date',
        'profile_photo_path',
        'current_belt_id',
        'primary_service_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joining_date' => 'date',
            'gender' => Gender::class,
            'status' => AccountStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentBelt(): BelongsTo
    {
        return $this->belongsTo(Belt::class, 'current_belt_id');
    }

    public function primaryService(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'primary_service_id');
    }

    public function beltHistories(): HasMany
    {
        return $this->hasMany(StudentBeltHistory::class);
    }

    public function beltTestAssignments(): HasMany
    {
        return $this->hasMany(BeltTestAssignment::class);
    }

    public function beltTests(): BelongsToMany
    {
        return $this->belongsToMany(BeltTest::class, 'belt_test_assignments')
            ->withPivot(['status', 'assigned_by', 'assigned_at', 'revoked_at', 'notes'])
            ->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(BeltTestApplication::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(BeltTestResult::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function competitionFormAssignments(): HasMany
    {
        return $this->hasMany(CompetitionFormAssignment::class);
    }

    public function competitionForms(): BelongsToMany
    {
        return $this->belongsToMany(CompetitionForm::class, 'competition_form_assignments')
            ->withPivot(['status', 'viewed_at', 'downloaded_at', 'response_status', 'assigned_by', 'assigned_at'])
            ->withTimestamps();
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::Active;
    }
}
