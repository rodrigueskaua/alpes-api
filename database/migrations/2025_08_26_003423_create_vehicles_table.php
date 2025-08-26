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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('external_id')->unique();
            $table->string('type', 50)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('version', 150)->nullable();
            $table->string('year_model', 4)->nullable();
            $table->string('year_build', 4)->nullable();
            $table->json('optionals')->nullable();
            $table->integer('doors')->nullable();
            $table->string('board', 20)->nullable();
            $table->string('chassi', 50)->nullable();
            $table->string('transmission', 50)->nullable();
            $table->integer('km')->nullable();
            $table->text('description')->nullable();
            $table->boolean('sold')->default(false);
            $table->string('category', 50)->nullable();
            $table->string('url_car', 150)->nullable();
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->string('color', 50)->nullable();
            $table->string('fuel', 50)->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
