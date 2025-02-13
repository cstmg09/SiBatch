<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string("company");
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->longText('message')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('inquiries_status', ['approved', 'rejected', 'pending'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
