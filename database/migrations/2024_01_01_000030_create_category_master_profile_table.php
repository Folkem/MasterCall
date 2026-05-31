<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_master_profile', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained('service_categories')->cascadeOnDelete();
            $table->foreignId('master_profile_id')->constrained('master_profiles')->cascadeOnDelete();
            $table->primary(['category_id', 'master_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_master_profile');
    }
};
