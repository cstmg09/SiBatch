<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InquiriesAddItemTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function it_can_add_products_to_inquiries_test(): void
    {
        $inquiries = Inquiries::factory()->create();
        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 200]);

        $inquiries->products()->attach([
            $product1->id => ['quantity' => 2],
            $product2->id => ['quantity' => 3],
        ]);

        expect($inquiries->products->count())->toBe(2);

        $total = $inquiries->products->sum(fn ($product) => $product->price * $product->pivot->quantity);

         expect($total)->toBe(800);
    }
}
