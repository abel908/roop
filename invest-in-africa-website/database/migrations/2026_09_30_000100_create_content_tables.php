<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Editable texts (pages, interface, emails) — EN / FR / 中文 side by side.
        Schema::create('content_translations', function (Blueprint $table) {
            $table->id();
            $table->string('group', 64)->index();
            $table->string('key');
            $table->text('en')->nullable();
            $table->text('fr')->nullable();
            $table->text('zh')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['group', 'key']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('seo_metas', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('noindex')->default(false);
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();
            $table->string('to_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedInteger('hits')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('number')->unique();
            $table->string('icon', 32);
            $table->json('slug');
            $table->json('title');
            $table->json('tagline')->nullable();
            $table->json('audience')->nullable();
            $table->json('intro')->nullable();
            $table->json('challenges')->nullable();
            $table->json('services')->nullable();
            $table->json('benefits')->nullable();
            $table->json('steps')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('documents')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 48)->unique();
            $table->json('name');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('website_url')->nullable();
            $table->string('category', 32)->default('strategic');
            $table->json('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('media_documents', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('file');
            $table->string('category', 32)->default('guide');
            $table->foreignId('domain_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('downloads')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_documents');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('sectors');
        Schema::dropIfExists('domains');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('seo_metas');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('content_translations');
    }
};
