<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('inquiries')->onDelete('cascade'); // Link to Inquiry
            $table->foreignId('payment_receipt_id')->constrained('payment_receipts')->onDelete('cascade'); // Link to Payment Receipt
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade'); // Link to Invoice
            $table->string('status')->default('pending'); // Work Order status
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
