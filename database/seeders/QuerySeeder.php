<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inquiries;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\PaymentReceipt;
use App\Models\WorkOrder;

class QuerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 products
        $products = Product::factory(5)->create();

        // Create 25 inquiries and attach products with random quantities
        Inquiries::factory(25)->create()->each(function ($inquiry) use ($products) {
            // Attach random products with random quantities
            $productAttachments = $products->random(2)->pluck('id')->mapWithKeys(function ($id) {
                return [$id => ['quantity' => rand(1, 5)]]; // Random quantity between 1 and 5
            })->toArray();

            $inquiry->products()->attach($productAttachments);

            // Calculate the total for the inquiry
            $total = $inquiry->products->sum(function ($product) {
                return $product->price * $product->pivot->quantity;
            });

            // Update the total in the inquiry
            $inquiry->update(['total' => $total]);

            // Assign a random status to the inquiry
            $inquiry->update(['inquiries_status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])]]);
        });

        // Generate invoices, purchase orders, payment receipts, and work orders for approved inquiries
        Inquiries::where('inquiries_status', 'approved')->get()->each(function ($inquiry) {
            // Create an invoice
            $invoice = Invoice::create([
                'inquiry_id' => $inquiry->id,
                'customer_id' => ucfirst($inquiry->name) . $inquiry->id, // Combine name + ID
                'status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])], // Assign random status
            ]);

            // Create a purchase order only if the invoice is approved
            if ($invoice->status === 'approved') {
                $purchaseOrder = PurchaseOrder::create([
                    'invoice_id' => $invoice->id,
                    'inquiry_id' => $inquiry->id,
                    'send_date' => now()->addDays(rand(1, 7)), // Random send date within the next week
                    'status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])], // Assign random status
                ]);

                // Create a payment receipt only if the purchase order is approved
                if ($purchaseOrder->status === 'approved') {
                    $randomFileName = 'payment_proofs/' . uniqid() . '.jpg';

                    $paymentReceipt = PaymentReceipt::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'invoice_id' => $invoice->id,
                        'inquiry_id' => $inquiry->id,
                        'payment_date' => now(), // Set payment date to current date
                        'payment_proof' => $randomFileName, // Placeholder for proof
                        'status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])], // Assign random status
                    ]);

                    // Create a work order only if the payment receipt is approved
                    if ($paymentReceipt->status === 'approved') {
                        WorkOrder::create([
                            'inquiry_id' => $inquiry->id,
                            'payment_receipt_id' => $paymentReceipt->id,
                            'invoice_id' => $invoice->id,
                            'status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])], // Assign random status
                        ]);
                    }
                }
            }
        });
    }
}
