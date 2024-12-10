<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade'); // Link to Invoice
            $table->foreignId('inquiry_id')->constrained('inquiries')->onDelete('cascade'); // Link to Inquiry
            $table->date('send_date'); // Date entered by the user
            $table->string('status')->default('pending'); // Purchase Order Status
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
