<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Inquiries;  // Corrected to use 'Inquiries' model
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_submit_an_inquiry_form()
    {
        // Create a product to be used in the inquiry form
        $product = Product::create([
            'name' => 'Sample Product',
            'description' => 'Sample product description',
            'price' => 2000,
            'image' => 'path/to/sample-image.jpg',
            'stock' => 10,  // Ensure stock is set to avoid error
        ]);

        // Prepare the form data with the created product ID
        $formData = [
            'name' => 'John Doe',
            'company' => 'Test Company',
            'email' => 'john.doe@example.com',
            'phone' => '123456789',
            'address' => '123 Test Street',
            'products' => [
                [
                    'id' => $product->id,  // Use the created product's ID
                    'quantity' => 2,
                ]
            ],
            'message' => 'This is a test inquiry message.',
        ];

        // Submit the form
        $response = $this->post(route('inquiries.store'), $formData);

        // Assert that the inquiry is saved in the database
        $this->assertDatabaseHas('inquiries', [
            'name' => 'John Doe',
            'company' => 'Test Company',
            'email' => 'john.doe@example.com',
            'phone' => '123456789',
            'address' => '123 Test Street',
            'message' => 'This is a test inquiry message.',
        ]);

        // Fetch the inquiry data from the database
        $inquiry = Inquiries::first();  // Corrected to reference 'Inquiries' model

        // The cart data is likely an array, not JSON-encoded
        $cartData = $inquiry->cart; // No need for json_decode if it's already an array

        // Expected cart data
        $expectedCartData = [
            [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format($product->price, 2, '.', ''),  // Ensure price is formatted as string with 2 decimal points
                'quantity' => 2,
            ]
        ];

        // Assert that the cart data matches
        $this->assertEquals($expectedCartData, $cartData);

        // Assert that the response is a redirect back to the previous page
        $response->assertRedirect();
    }
}
