<?php

namespace App\Http\Controllers;

use App\Models\Inquiries;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InquiriesController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.id' => 'nullable|exists:products,id',
            'products.*.quantity' => 'nullable|integer|min:1',
            'message' => 'nullable|string',
        ]);

        // Create the inquiry with validated data
        $inquiry = Inquiries::create([
            'name' => $validatedData['name'],
            'company' => $validatedData['company'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'message' => $validatedData['message'],
            'total' => 0, // Total will be calculated after attaching products
        ]);

        // Extract product IDs and fetch products in bulk
        $productIds = collect($validatedData['products'])->pluck('id')->filter();
        $products = Product::findMany($productIds)->keyBy('id');

        // Prepare products to attach and calculate total
        $productsToAttach = [];
        $total = 0;

        foreach ($validatedData['products'] as $productData) {
            $productId = $productData['id'] ?? null;
            if ($productId && $products->has($productId)) {
                $product = $products[$productId];
                $quantity = $productData['quantity'] ?? 1;

                // Attach product with quantity and calculate total
                $productsToAttach[$product->id] = ['quantity' => $quantity];
                $total += $product->price * $quantity;

                Log::info("Product ID: {$product->id}, Quantity: {$quantity}, Subtotal: " . ($product->price * $quantity));
            } else {
                Log::warning("Invalid or missing product ID: " . ($productId ?? 'NULL'));
            }
        }

        // Attach products to the inquiry
        $inquiry->products()->attach($productsToAttach);

        // Update the inquiry total
        $inquiry->update(['total' => $total]);

        Log::info("Inquiry ID: {$inquiry->id}, Total: $total");

        // Redirect with success message
        return redirect()->route('home', $inquiry->id)->with('success', 'Your inquiry has been sent successfully!');
    }
}
