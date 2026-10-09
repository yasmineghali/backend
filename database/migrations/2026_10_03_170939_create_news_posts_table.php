<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            $table->string('title_fr');
            $table->text('excerpt_fr')->nullable();
            $table->longText('content_fr');

            $table->string('title_en')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('content_en')->nullable();

            $table->string('title_de')->nullable();
            $table->text('excerpt_de')->nullable();
            $table->longText('content_de')->nullable();

            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_posts');
    }
};
