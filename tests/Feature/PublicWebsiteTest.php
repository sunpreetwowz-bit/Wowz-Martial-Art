<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Service;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_dynamic_services(): void
    {
        Service::query()->create([
            'name' => 'Taekwondo',
            'slug' => 'taekwondo',
            'description' => 'Traditional taekwondo training.',
            'display_order' => 1,
            'status' => AccountStatus::Active,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Wowz Martial Art')
            ->assertSee('Taekwondo')
            ->assertSee('Choose your program');
    }

    public function test_contact_form_can_be_submitted(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'phone' => '9876543210',
            'subject' => 'Trial class',
            'message' => 'I would like to book a trial class.',
        ])->assertRedirect(route('contact.create'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contacts', [
            'email' => 'visitor@example.com',
            'subject' => 'Trial class',
            'status' => 'unread',
        ]);
    }

    public function test_admin_can_manage_services_and_guest_cannot(): void
    {
        $this->get(route('admin.services.index'))->assertRedirect(route('login'));

        $student = Student::factory()->create();
        $this->actingAs($student->user)
            ->get(route('admin.services.index'))
            ->assertForbidden();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.services.store'), [
            'name' => 'Kick Boxing',
            'description' => 'High intensity striking classes.',
            'display_order' => 2,
            'status' => AccountStatus::Active->value,
        ])->assertRedirect(route('admin.services.index'));

        $this->assertDatabaseHas('services', [
            'name' => 'Kick Boxing',
            'slug' => 'kick-boxing',
        ]);
    }
}
