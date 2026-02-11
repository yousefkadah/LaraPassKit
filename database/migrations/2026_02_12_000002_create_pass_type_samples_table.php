<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pass_type_samples', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('pass_type');
            $table->string('platform')->nullable();
            $table->json('fields');
            $table->json('images');
            $table->timestamps();

            $table->index(['pass_type', 'platform', 'source']);
            $table->index(['owner_user_id', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pass_type_samples');
    }
};
