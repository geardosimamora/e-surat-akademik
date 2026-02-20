<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('nim')->unique()->nullable();
            $table->string('password');
            $table->string('role')->default('student'); 
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index(['nim', 'role']); 
        });

        // 2. Password Reset & Sessions (Wajib untuk Laravel 11 + Filament)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 3. Letter Types
        Schema::create('letter_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('template_view'); 
            $table->boolean('requires_approval')->default(true);
            $table->timestamps();
        });

        // 4. Letters (Transactions)
        Schema::create('letters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('letter_type_id')->constrained();
            
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected'])->default('pending')->index(); 
            
            $table->string('letter_number')->nullable()->unique();
            $table->text('rejection_note')->nullable();
            
            $table->json('user_snapshot'); 
            $table->json('additional_data')->nullable(); 
            
            $table->string('file_path')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable(); 
            
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        // 5. Counters
        Schema::create('letter_counters', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->integer('last_number')->default(0);
            $table->unique(['year', 'month']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_counters');
        Schema::dropIfExists('letters');
        Schema::dropIfExists('letter_types');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};