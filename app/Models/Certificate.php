<?php

namespace App\Models;

use App\Enums\CertificateStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'certificate_number',
        'verification_code',
        'student_id',
        'belt_test_id',
        'belt_test_result_id',
        'belt_id',
        'student_name_snapshot',
        'student_code_snapshot',
        'belt_name_snapshot',
        'test_date',
        'issued_on',
        'authorized_by_name',
        'signature_path',
        'file_path',
        'status',
        'revoked_at',
        'revoke_reason',
        'issued_by',
    ];

    protected function casts(): array
    {
        return [
            'test_date' => 'date',
            'issued_on' => 'date',
            'status' => CertificateStatus::class,
            'revoked_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function beltTest(): BelongsTo
    {
        return $this->belongsTo(BeltTest::class);
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(BeltTestResult::class, 'belt_test_result_id');
    }

    public function belt(): BelongsTo
    {
        return $this->belongsTo(Belt::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function getRouteKeyName(): string
    {
        return 'certificate_number';
    }

    public function isIssued(): bool
    {
        return $this->status === CertificateStatus::Issued;
    }
}
