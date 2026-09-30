<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->string('locale', 5);
            $table->string('status', 24)->default('received')->index();
            // Step 1 — project holder
            $table->string('full_name');
            $table->string('organization');
            $table->string('email');
            $table->string('phone', 32);
            $table->char('country', 2);
            $table->string('position');
            // Step 2 — project
            $table->string('project_name');
            $table->char('project_country', 2);
            $table->foreignId('sector_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description');
            $table->string('stage', 24);
            $table->decimal('investment_amount', 16, 2);
            $table->char('currency', 3);
            $table->string('funding_type', 24);
            $table->string('timeline');
            // Step 3 — validation
            $table->timestamp('consent_at');
            $table->timestamp('certified_at');
            // Processing
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('submission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type', 128);
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->json('use_of_funds')->nullable();
            $table->json('impact')->nullable();
            $table->json('timeline')->nullable();
            $table->char('country', 2)->index();
            $table->string('region', 24)->index();
            $table->foreignId('sector_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('domain_id')->nullable()->constrained()->nullOnDelete();
            $table->string('stage', 24)->index();
            $table->decimal('investment_amount', 16, 2)->index();
            $table->char('currency', 3)->default('USD');
            $table->string('funding_type', 24)->index();
            $table->string('status', 24)->default('open')->index();
            $table->boolean('description_on_request')->default(false);
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('public_document')->nullable();
            $table->string('confidential_document')->nullable();
            $table->unsignedSmallInteger('jobs_expected')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('submission_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('interest_expressions', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('status', 24)->default('new')->index();
            $table->string('full_name');
            $table->string('organization')->nullable();
            $table->string('investor_type', 24);
            $table->string('email');
            $table->string('phone', 32);
            $table->char('country', 2);
            $table->decimal('amount', 16, 2)->nullable();
            $table->text('message')->nullable();
            $table->timestamp('consent_at');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->string('locale', 5);
            $table->string('status', 24)->default('new')->index();
            $table->string('subject', 24)->index();
            $table->string('full_name');
            $table->string('organization')->nullable();
            $table->string('email');
            $table->string('phone', 32)->nullable();
            $table->char('country', 2)->nullable();
            $table->text('message');
            $table->timestamp('consent_at');
            $table->text('internal_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 64)->index();
            $table->nullableMorphs('subject');
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('interest_expressions');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('submission_documents');
        Schema::dropIfExists('submissions');
    }
};
