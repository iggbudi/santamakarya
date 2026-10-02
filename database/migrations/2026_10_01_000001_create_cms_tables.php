<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('path')->unique();
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size_bytes');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->string('alt_text')->default('');
            $table->string('original_name');
            $table->timestamps();
        });
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->enum('location', ['hero', 'about']);
            $table->foreignId('media_asset_id')->nullable()->constrained()->restrictOnDelete();
            $table->text('image_url')->nullable();
            $table->string('alt_text')->default('');
            $table->unsignedTinyInteger('position_x')->default(50);
            $table->unsignedTinyInteger('position_y')->default(50);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['location', 'is_active', 'sort_order']);
        });
        Schema::create('portfolio_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('category_id')->constrained('portfolio_categories')->restrictOnDelete();
            $table->foreignId('cover_media_asset_id')->nullable()->constrained('media_assets')->restrictOnDelete();
            $table->text('image_url')->nullable();
            $table->string('alt_text')->default('');
            $table->string('caption')->default('Project Gallery');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('page_versions', function (Blueprint $table) {
            $table->id();
            $table->json('payload');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->json('draft_payload');
            $table->foreignId('published_version_id')->nullable()->constrained('page_versions')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['site_settings', 'page_versions', 'portfolio_projects', 'portfolio_categories', 'slides', 'media_assets'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
    }
};
