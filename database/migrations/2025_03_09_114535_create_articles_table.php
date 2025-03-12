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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id');
            $table->string('author')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('url',1000); //length 1000 since sometimes the url is very long
            $table->string('url_to_image',1000)->nullable();//length 1000 since sometimes the url is very long
            $table->timestamp('published_at')->nullable();
            $table->string('provider')->nullable(); // To track the provider (e.g., Mediapart, Novinky.cz, NYT)
            $table->string('type')->nullable(); // Article type (e.g., liveblog, article, briefing)
            $table->string('category')->nullable(); // Add category for filtering
            $table->json('additional_data')->nullable(); // Store any extra data (e.g., tags, specific provider fields)

            $table->timestamps();

            $table->index(['source_id']);
            $table->index(['published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
