<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pages composed from validated blocks (§8.1): drafts, preview, scheduled publication.
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('slug');
            $table->json('blocks')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('status', 16)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Version history and restoration (§8.1).
        Schema::create('revisions', function (Blueprint $table) {
            $table->id();
            $table->morphs('revisionable');
            $table->json('snapshot');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        // Menus administrable per language (§7.2, §8.1).
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('location', 24)->index();
            $table->json('label')->nullable();
            $table->string('type', 16)->default('route');
            $table->string('target')->nullable();
            $table->foreignId('page_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('url')->nullable();
            $table->boolean('new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        // Media library: images, videos, documents with alternative texts per language (§8.1, §8.2).
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->string('type', 16)->index();
            $table->string('mime_type', 128)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->json('alt')->nullable();
            $table->json('variants')->nullable();
            $table->string('poster')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('revisions');
        Schema::dropIfExists('pages');
    }
};
