<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Full name" name="name" :value="old('name', $student?->user?->name)" required />
    <x-admin.input label="Email (login)" name="email" type="email" :value="old('email', $student?->user?->email)" required />
    <x-admin.input label="Password {{ $student ? '(leave blank to keep)' : '(optional — auto-generated if empty)' }}" name="password" type="password" />
    <x-admin.input label="Confirm password" name="password_confirmation" type="password" />
    <x-admin.input label="Phone" name="phone" :value="old('phone', $student?->phone)" />
    <x-admin.input label="Date of birth" name="date_of_birth" type="date" :value="old('date_of_birth', optional($student?->date_of_birth)->format('Y-m-d'))" />
    <x-admin.input label="Gender" name="gender" type="select">
        <option value="">Select gender</option>
        @foreach ($genders as $gender)
            <option value="{{ $gender->value }}" @selected(old('gender', $student?->gender?->value) === $gender->value)>
                {{ $gender->label() }}
            </option>
        @endforeach
    </x-admin.input>
    <x-admin.input label="Joining date" name="joining_date" type="date" :value="old('joining_date', optional($student?->joining_date)->format('Y-m-d') ?? now()->toDateString())" />
    <x-admin.input label="Current belt" name="current_belt_id" type="select">
        <option value="">Not assigned</option>
        @foreach ($belts as $belt)
            <option value="{{ $belt->id }}" @selected((string) old('current_belt_id', $student?->current_belt_id) === (string) $belt->id)>
                {{ $belt->name }}
            </option>
        @endforeach
    </x-admin.input>
    <x-admin.input label="Primary service" name="primary_service_id" type="select">
        <option value="">Not assigned</option>
        @foreach ($services as $service)
            <option value="{{ $service->id }}" @selected((string) old('primary_service_id', $student?->primary_service_id) === (string) $service->id)>
                {{ $service->name }}
            </option>
        @endforeach
    </x-admin.input>
    <x-admin.input label="Status" name="status" type="select" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $student?->status?->value ?? 'active') === $status->value)>
                {{ $status->label() }}
            </option>
        @endforeach
    </x-admin.input>
    <x-admin.input label="Profile photo" name="profile_photo" type="file" accept="image/*" />
</div>

@if ($student)
    <x-admin.input label="Belt change notes" name="belt_change_notes" type="textarea" :value="old('belt_change_notes')" placeholder="Required context if changing the official belt." />
@else
    <x-admin.input label="Student ID (optional)" name="student_code" :value="old('student_code')" placeholder="Auto-generated if left blank" />
@endif

<x-admin.input label="Address" name="address" type="textarea" :value="old('address', $student?->address)" />

<div class="grid gap-4 md:grid-cols-2">
    <x-admin.input label="Emergency contact name" name="emergency_contact_name" :value="old('emergency_contact_name', $student?->emergency_contact_name)" />
    <x-admin.input label="Emergency contact phone" name="emergency_contact_phone" :value="old('emergency_contact_phone', $student?->emergency_contact_phone)" />
</div>

<x-admin.input label="Internal notes" name="notes" type="textarea" :value="old('notes', $student?->notes)" />
