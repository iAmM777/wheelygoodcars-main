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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('license_plate');
            $table->string('brand');
            $table->string('model');
            $table->decimal('price');
            $table->integer('mileage');
            $table->integer('seats')->nullable();
            $table->integer('doors')->nullable();
            $table->integer('production_year')->nullable();
            $table->integer('weight')->nullable();
            $table->string('color')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
