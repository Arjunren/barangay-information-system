<?php

namespace Tests\Feature;

use App\Models\CertificateRequest;
use App\Models\Household;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BarangayApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_can_register_and_receives_token(): void
    {
        $this->postJson('/api/auth/register', ['name' => 'New Resident', 'email' => 'new@example.com', 'password' => 'StrongPassword123!', 'password_confirmation' => 'StrongPassword123!'])->assertCreated()->assertJsonPath('data.user.role', 'resident')->assertJsonStructure(['data' => ['token']]);
        $this->assertDatabaseHas('users', ['email' => 'new@example.com', 'role' => 'resident']);
    }

    public function test_staff_can_create_household_and_resident(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'active' => true]);
        Sanctum::actingAs($staff);
        $householdId = $this->postJson('/api/households', ['household_number' => 'HH-100', 'address' => 'Main Road', 'zone' => 'Zone 2'])->assertSuccessful()->json('data.id');
        $this->postJson('/api/residents', ['household_id' => $householdId, 'resident_number' => 'RES-100', 'first_name' => 'Ana', 'last_name' => 'Santos', 'birth_date' => '1995-05-10', 'sex' => 'female', 'civil_status' => 'single', 'registered_voter' => true])->assertSuccessful()->assertJsonPath('data.resident_number', 'RES-100');
    }

    public function test_resident_cannot_view_another_residents_certificate(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'active' => true]);
        $owner = User::factory()->create(['role' => 'resident', 'active' => true]);
        $other = User::factory()->create(['role' => 'resident', 'active' => true]);
        $household = Household::create(['household_number' => 'HH-1', 'address' => 'Road', 'zone' => 'One', 'created_by' => $staff->id]);
        $resident = Resident::create(['user_id' => $owner->id, 'household_id' => $household->id, 'resident_number' => 'R-1', 'first_name' => 'One', 'last_name' => 'Owner', 'birth_date' => '1990-01-01', 'sex' => 'male', 'civil_status' => 'single', 'registered_voter' => true, 'created_by' => $staff->id]);
        $certificate = CertificateRequest::create(['resident_id' => $resident->id, 'certificate_type' => 'residency', 'purpose' => 'Bank']);
        Sanctum::actingAs($other);
        $this->getJson('/api/certificates/'.$certificate->id)->assertForbidden();
    }

    public function test_certificate_status_transitions_are_enforced(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'active' => true]);
        $residentUser = User::factory()->create(['role' => 'resident', 'active' => true]);
        $resident = Resident::create(['user_id' => $residentUser->id, 'resident_number' => 'R-2', 'first_name' => 'Test', 'last_name' => 'Resident', 'birth_date' => '1990-01-01', 'sex' => 'other', 'civil_status' => 'single', 'registered_voter' => false, 'created_by' => $staff->id]);
        $certificate = CertificateRequest::create(['resident_id' => $resident->id, 'certificate_type' => 'indigency', 'purpose' => 'Assistance']);
        Sanctum::actingAs($staff);
        $this->patchJson('/api/certificates/'.$certificate->id, ['status' => 'released'])->assertStatus(409);
        $this->patchJson('/api/certificates/'.$certificate->id, ['status' => 'approved'])->assertOk()->assertJsonPath('data.status', 'approved');
        $this->patchJson('/api/certificates/'.$certificate->id, ['status' => 'released'])->assertOk()->assertJsonPath('data.status', 'released');
    }

    public function test_standard_user_cannot_access_resident_administration(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'resident', 'active' => true]));
        $this->getJson('/api/residents')->assertForbidden();
    }
}
