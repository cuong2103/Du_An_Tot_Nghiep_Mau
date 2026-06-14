<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->unique()->constrained('appointments')->onDelete('cascade');
            $table->foreignId('patient_profile_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('doctor_profile_id')->nullable()->index()->constrained('doctor_profiles')->onDelete('set null');
            $table->integer('specialty_id')->index();
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
