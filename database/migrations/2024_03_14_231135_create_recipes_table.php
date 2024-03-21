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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->index();
            $table->string('serving');
            $table->integer('prep_time')->default(0);
            $table->integer('cook_time')->default(0);
            $table->integer('total_time')->storedAs('cook_time + prep_time');
            $table->longText('ingredients');
            $table->longText('instructions');
            $table->longText('description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
