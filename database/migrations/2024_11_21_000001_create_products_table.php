<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable(); // Allow null for images if optional
            $table->longText('description')->nullable(); // Allow null for descriptions if optional
            $table->decimal('price', 10, 2)->default(0); // Ensure price defaults to 0 if not provided
            $table->integer('stock')->default(0); // Ensure stock defaults to 0
            $table->boolean('is_available')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
