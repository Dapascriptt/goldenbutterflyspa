<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapist_charges_archive', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('therapist_name');
            $table->unsignedInteger('extra_time')->default(0);
            $table->unsignedInteger('extra_charge')->default(0);
            $table->unsignedInteger('traditional')->default(0);
            $table->unsignedInteger('fullbody')->default(0);
            $table->unsignedInteger('butterfly')->default(0);
            $table->boolean('shockwave')->default(false);
            $table->unsignedInteger('discount_percent')->default(0);
            $table->unsignedInteger('discount_nominal')->default(0);
            $table->unsignedInteger('room_charge')->default(0);
            $table->unsignedInteger('total_charge')->default(0);
            $table->string('room')->nullable();
            $table->timestamps();

            $table->index('date', 'therapist_charges_archive_date_idx');
            $table->index('therapist_name', 'therapist_charges_archive_name_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapist_charges_archive');
    }
};
