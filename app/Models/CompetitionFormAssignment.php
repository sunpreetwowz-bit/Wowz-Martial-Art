<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use App\Enums\CompetitionResponseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionFormAssignment extends Model
{
    protected $fillable = [
        'competition_form_id',
        'student_id',
        'status',
        'viewed_at',
        'downloaded_at',
        'response_status',
        'assigned_by',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssignmentStatus::class,
            'response_status' => CompetitionResponseStatus::class,
            'viewed_at' => 'datetime',
            'downloaded_at' => 'datetime',
            'assigned_at' => 'datetime',
        ];
    }

    public function competitionForm(): BelongsTo
    {
        return $this->belongsTo(CompetitionForm::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
