<?php

namespace App\Services;

use App\Enums\AccountStatus;
use App\Enums\BeltHistorySource;
use App\Enums\UserRole;
use App\Models\Student;
use App\Models\StudentBeltHistory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentService
{
    protected $codeGenerator;
    protected $auditLogger;

    public function __construct(
        StudentCodeGenerator $codeGenerator,
        AuditLogger $auditLogger,
    )
    {
        $this->codeGenerator = $codeGenerator;
        $this->auditLogger = $auditLogger;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{student: Student, temporary_password: string}
     */
    public function create(array $data, User $actor)
    {
        $temporaryPassword = $data['password'] ?? Str::password(12);

        $student = DB::transaction(function () use ($data, $actor, $temporaryPassword) {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($temporaryPassword),
                'role' => UserRole::Student,
                'status' => AccountStatus::from($data['status'] ?? AccountStatus::Active->value),
                'email_verified_at' => now(),
            ]);

            $photoPath = null;
            if (($data['profile_photo'] ?? null) instanceof UploadedFile) {
                $photoPath = $data['profile_photo']->store('students/photos', 'public');
            }

            $student = Student::query()->create([
                'user_id' => $user->id,
                'student_code' => $data['student_code'] ?? $this->codeGenerator->generate(),
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'joining_date' => $data['joining_date'] ?? now()->toDateString(),
                'profile_photo_path' => $photoPath,
                'current_belt_id' => $data['current_belt_id'] ?? null,
                'primary_service_id' => $data['primary_service_id'] ?? null,
                'status' => AccountStatus::from($data['status'] ?? AccountStatus::Active->value),
                'notes' => $data['notes'] ?? null,
            ]);

            if ($student->current_belt_id) {
                StudentBeltHistory::query()->create([
                    'student_id' => $student->id,
                    'from_belt_id' => null,
                    'to_belt_id' => $student->current_belt_id,
                    'source' => BeltHistorySource::Initial,
                    'notes' => 'Initial belt assignment on student creation.',
                    'created_by' => $actor->id,
                ]);
            }

            $this->auditLogger->log(
                action: 'student.created',
                subject: $student,
                newValues: $student->only([
                    'student_code',
                    'current_belt_id',
                    'primary_service_id',
                    'status',
                ]) + ['email' => $user->email, 'name' => $user->name],
                actor: $actor,
            );

            return $student->load(['user', 'currentBelt', 'primaryService']);
        });

        return [
            'student' => $student,
            'temporary_password' => $temporaryPassword,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Student $student, array $data, User $actor) {
        return DB::transaction(function () use ($student, $data, $actor) {
            $student->loadMissing('user');

            $old = [
                'name' => $student->user->name,
                'email' => $student->user->email,
                'current_belt_id' => $student->current_belt_id,
                'status' => $student->status?->value,
            ];

            $userStatus = AccountStatus::from($data['status'] ?? $student->status->value);

            $student->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'status' => $userStatus,
            ]);

            if (! empty($data['password'])) {
                $student->user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            if (($data['profile_photo'] ?? null) instanceof UploadedFile) {
                if ($student->profile_photo_path) {
                    Storage::disk('public')->delete($student->profile_photo_path);
                }
                $data['profile_photo_path'] = $data['profile_photo']->store('students/photos', 'public');
            }

            $previousBeltId = $student->current_belt_id;
            $newBeltId = $data['current_belt_id'] ?? null;

            $student->update([
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'joining_date' => $data['joining_date'] ?? $student->joining_date,
                'profile_photo_path' => $data['profile_photo_path'] ?? $student->profile_photo_path,
                'current_belt_id' => $newBeltId,
                'primary_service_id' => $data['primary_service_id'] ?? null,
                'status' => $userStatus,
                'notes' => $data['notes'] ?? null,
            ]);

            if ((int) $previousBeltId !== (int) $newBeltId && $newBeltId) {
                StudentBeltHistory::query()->create([
                    'student_id' => $student->id,
                    'from_belt_id' => $previousBeltId,
                    'to_belt_id' => $newBeltId,
                    'source' => BeltHistorySource::AdminCorrection,
                    'notes' => $data['belt_change_notes'] ?? 'Belt updated by admin.',
                    'created_by' => $actor->id,
                ]);

                $this->auditLogger->log(
                    action: 'student.belt_changed',
                    subject: $student,
                    oldValues: ['current_belt_id' => $previousBeltId],
                    newValues: ['current_belt_id' => $newBeltId],
                    actor: $actor,
                );
            }

            $this->auditLogger->log(
                action: 'student.updated',
                subject: $student,
                oldValues: $old,
                newValues: [
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                    'current_belt_id' => $student->current_belt_id,
                    'status' => $student->status?->value,
                ],
                actor: $actor,
            );

            return $student->fresh(['user', 'currentBelt', 'primaryService']);
        });
    }

    public function deactivate(Student $student, User $actor) {
        return DB::transaction(function () use ($student, $actor) {
            $student->loadMissing('user');

            $student->update(['status' => AccountStatus::Inactive]);
            $student->user->update(['status' => AccountStatus::Inactive]);

            $this->auditLogger->log(
                action: 'student.deactivated',
                subject: $student,
                oldValues: ['status' => AccountStatus::Active->value],
                newValues: ['status' => AccountStatus::Inactive->value],
                actor: $actor,
            );

            return $student->fresh(['user']);
        });
    }

    public function activate(Student $student, User $actor) {
        return DB::transaction(function () use ($student, $actor) {
            $student->loadMissing('user');

            $student->update(['status' => AccountStatus::Active]);
            $student->user->update(['status' => AccountStatus::Active]);

            $this->auditLogger->log(
                action: 'student.activated',
                subject: $student,
                oldValues: ['status' => AccountStatus::Inactive->value],
                newValues: ['status' => AccountStatus::Active->value],
                actor: $actor,
            );

            return $student->fresh(['user']);
        });
    }
}
