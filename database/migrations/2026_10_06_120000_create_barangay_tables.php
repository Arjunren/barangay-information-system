<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->string('household_number')->unique();
            $table->string('address');
            $table->string('zone', 80)->index();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->foreignId('household_id')->nullable()->constrained()->nullOnDelete();
            $table->string('resident_number')->unique();
            $table->string('first_name');
            $table->string('last_name')->index();
            $table->date('birth_date');
            $table->string('sex', 20);
            $table->string('civil_status', 30);
            $table->string('phone', 30)->nullable();
            $table->boolean('registered_voter')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->index(['household_id', 'last_name']);
        });
        Schema::create('certificate_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_type', 80);
            $table->string('purpose');
            $table->string('status', 20)->default('pending')->index();
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reported_by')->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->timestamp('occurred_at');
            $table->string('status', 20)->default('open')->index();
            $table->timestamps();
        });
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('subject_type', 100)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('incident_reports');
        Schema::dropIfExists('certificate_requests');
        Schema::dropIfExists('residents');
        Schema::dropIfExists('households');
    }
};
