<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade'); // Link to Purchase Order
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade'); // Link to Invoice
            $table->foreignId('inquiry_id')->constrained('inquiries')->onDelete('cascade'); // Link to Inquiry
            $table->date('payment_date'); // To be input when creating the receipt
            $table->string('payment_proof'); // Path to the PDF file
            $table->string('status')->default('pending'); // Status of the payment receipt
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
