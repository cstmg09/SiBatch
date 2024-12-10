<?php

namespace App\Filament\Resources\InquiriesResource\Pages;

use App\Filament\Resources\InquiriesResource;
use App\Models\Inquiries;
use Filament\Resources\Pages\CreateRecord;

class CreateInquiries extends CreateRecord
{
    protected static string $resource = InquiriesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract products
        $products = $data['products'] ?? [];
        unset($data['products']); // Remove products to avoid direct DB insertion

        // Calculate total dynamically
        $data['total'] = collect($products)->sum(function ($product) {
            return ($product['price'] ?? 0) * ($product['quantity'] ?? 0);
        });

        return $data;
    }

    protected function afterCreate(): void
    {
        // Attach products to the inquiry
        $products = $this->data['products'] ?? [];
        foreach ($products as $product) {
            $this->record->products()->attach($product['product_id'], [
                'quantity' => $product['quantity'],
            ]);
        }
    }
}
