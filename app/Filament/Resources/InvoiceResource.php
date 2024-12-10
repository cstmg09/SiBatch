<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationGroup = 'Orders Management';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false; // Disable the "New Invoice" button
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('status')
                ->label(__('Status'))
                ->options([
                    'pending' => __('Pending'),
                    'approved' => __('Approved'),
                    'rejected' => __('Rejected'),
                ])
                ->required()
                ->default('pending')
                ->disabled(fn ($record) => in_array($record?->status, ['approved', 'rejected'])), // Disable if approved or rejected
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Invoice::whereHas('inquiry') // Ensures only invoices with an associated inquiry are included
            )
            ->columns([
                TextColumn::make('id')
                    ->label(__('Invoice ID'))
                    ->sortable(),

                TextColumn::make('inquiry.name')
                    ->label(__('Name')),

                TextColumn::make('inquiry.company')
                    ->label(__('Company')),

                TextColumn::make('inquiry.address')
                    ->label(__('Address')),

                TextColumn::make('inquiry.phone')
                    ->label(__('Phone')),

                TextColumn::make('inquiry.message')
                    ->label(__('Message')),

                TextColumn::make('customer_id')
                    ->label(__('Customer ID')),

                TextColumn::make('inquiry.total')
                    ->label(__('Total'))
                    ->money('idr')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),

                TextColumn::make('products')
                    ->label(__('Products'))
                    ->formatStateUsing(fn ($state, Invoice $record) =>
                        $record->products
                            ->map(fn ($product) => "{$product->name} (Qty: {$product->pivot->quantity}) ({Price: {$product->price}})")
                            ->implode(', ')
                    ),
            ])
            ->actions([
                EditAction::make(),

                Action::make('Make Purchase Order')
                    ->form([
                        DatePicker::make('send_date')
                            ->label(__('Send Date'))
                            ->required(),
                    ])
                    ->action(fn (Invoice $record, array $data) => self::createPurchaseOrder($record, $data))
                    ->requiresConfirmation()
                    ->visible(fn (Invoice $record) => $record->status === 'approved')
                    ->color('primary'),

                // Download PDF Button
                Action::make('Download PDF')
                    ->color('success') // Set button color to green
                    ->url(fn (Invoice $record) => route('invoice.pdf', $record->id)) // Generate the URL for the PDF
                    ->openUrlInNewTab() // Open in a new tab
                    ->visible(fn (Invoice $record) => $record->status === 'approved'), // Show only if the invoice is approved
            ])
            ->bulkActions([]); // No bulk actions for now
    }

    protected static function createPurchaseOrder(Invoice $record, array $data)
    {
        if (PurchaseOrder::where('invoice_id', $record->id)->exists()) {
            Notification::make()
                ->title(__('Error'))
                ->body(__('A Purchase Order already exists for this invoice.'))
                ->danger()
                ->send();
            return;
        }

        PurchaseOrder::create([
            'invoice_id' => $record->id,
            'inquiry_id' => $record->inquiry_id,
            'send_date' => $data['send_date'],
        ]);

        Notification::make()
            ->title(__('Success'))
            ->body(__('Purchase Order created successfully!'))
            ->success()
            ->send();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
