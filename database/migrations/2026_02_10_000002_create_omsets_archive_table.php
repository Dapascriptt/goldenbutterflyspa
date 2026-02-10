<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omsets_archive', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index('date', 'omsets_archive_date_idx');
            $table->index('code', 'omsets_archive_code_idx');
            $table->index('created_by', 'omsets_archive_created_by_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omsets_archive');
    }
};
