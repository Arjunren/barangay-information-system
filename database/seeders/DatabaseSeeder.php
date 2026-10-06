<?php

namespace Database\Seeders;

use App\Models\CertificateRequest;
use App\Models\Household;
use App\Models\IncidentReport;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@example.com'], ['name' => 'Barangay Admin', 'password' => Hash::make('AdminPassword123!'), 'role' => 'admin', 'active' => true]);
        $staff = User::updateOrCreate(['email' => 'staff@example.com'], ['name' => 'Barangay Staff', 'password' => Hash::make('StaffPassword123!'), 'role' => 'staff', 'active' => true]);
        $residentUser = User::updateOrCreate(['email' => 'resident@example.com'], ['name' => 'Juan Dela Cruz', 'password' => Hash::make('ResidentPassword123!'), 'role' => 'resident', 'active' => true]);
        $household = Household::updateOrCreate(['household_number' => 'HH-0001'], ['address' => '123 Mabini Street', 'zone' => 'Zone 1', 'created_by' => $admin->id]);
        $resident = Resident::updateOrCreate(['resident_number' => 'RES-0001'], ['user_id' => $residentUser->id, 'household_id' => $household->id, 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'birth_date' => '1990-01-15', 'sex' => 'male', 'civil_status' => 'married', 'phone' => '09170000000', 'registered_voter' => true, 'created_by' => $staff->id, 'updated_by' => $staff->id]);
        CertificateRequest::firstOrCreate(['resident_id' => $resident->id, 'certificate_type' => 'barangay_clearance', 'purpose' => 'Employment'], ['status' => 'pending']);
        IncidentReport::firstOrCreate(['reported_by' => $residentUser->id, 'title' => 'Street light outage'], ['description' => 'Street light has not worked for two nights.', 'location' => 'Mabini Street', 'occurred_at' => now()->subDay(), 'status' => 'open']);
    }
}
