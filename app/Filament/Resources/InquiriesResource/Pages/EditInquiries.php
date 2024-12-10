<?php

namespace App\Filament\Resources\InquiriesResource\Pages;

use App\Filament\Resources\InquiriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Product;

class EditInquiries extends EditRecord
{
    protected static string $resource = InquiriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Map inquiry_product pivot data into the 'products' repeater
        $data['products'] = $this->record->products->map(function ($product) {
            return [
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
            ];
        })->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract 'products' from form data
        $products = $data['products'] ?? [];
        unset($data['products']); // Remove 'products' to avoid saving in the main inquiries table

        // Sync the products relationship
        $this->record->products()->sync(
            collect($products)->mapWithKeys(function ($product) {
                return [$product['product_id'] => ['quantity' => $product['quantity']]];
            })->toArray()
        );

        return $data;
    }
}
