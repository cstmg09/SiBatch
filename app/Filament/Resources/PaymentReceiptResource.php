<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentReceiptResource\Pages;
use App\Models\PaymentReceipt;
use App\Models\WorkOrder;
use App\Models\Invoice;
use App\Models\Inquiries;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;

class PaymentReceiptResource extends Resource
{
    protected static ?string $model = PaymentReceipt::class;

    protected static ?string $navigationGroup = 'Orders Management';
    protected static ?int $navigationSort = 5;

    public const DEFAULT_STATUS = 'pending';

    public static function canCreate(): bool
    {
        return false; // Disable the "New Payment Receipt" button
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            DatePicker::make('payment_date')
                ->label(__('Payment Date'))
                ->required(),

            FileUpload::make('payment_proof')
                ->label(__('Payment Proof'))
                ->directory('payment_proofs') // Save in `storage/app/payment_proofs`
                ->image() // Restrict to image files
                ->required(),

            Select::make('status')
                ->label(__('Status'))
                ->options([
                    'pending' => __('Pending'),
                    'approved' => __('Approved'),
                    'rejected' => __('Rejected'),
                ])
                ->required()
                ->default(self::DEFAULT_STATUS)
                ->disabled(fn ($record) => in_array($record?->status, ['approved', 'rejected'])), // Disable if approved or rejected
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                PaymentReceipt::whereHas('inquiry') // Ensures the purchase order has an associated inquiry
                    ->whereHas('invoice')         // Ensures the purchase order has an associated invoice
                    ->whereHas('purchaseOrder')
            )
            ->columns([
                TextColumn::make('id')
                    ->label(__('Receipt ID'))
                    ->sortable(),

                TextColumn::make('purchaseOrder.id')
                    ->label(__('Purchase Order ID')),

                TextColumn::make('invoice.id')
                    ->label(__('Invoice ID')),

                TextColumn::make('inquiry.name')
                    ->label(__('Customer Name')),

                TextColumn::make('payment_date')
                    ->label(__('Payment Date'))
                    ->date(),

                TextColumn::make('payment_proof')
                    ->label(__('Payment Proof'))
                    ->url(fn ($record) => Storage::url($record->payment_proof)) // Use dynamic URL for file
                    ->openUrlInNewTab(),

                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->actions([
                EditAction::make(),

                Action::make('Make Work Order')
                    ->action(fn (PaymentReceipt $record) => self::createWorkOrder($record))
                    ->requiresConfirmation()
                    ->visible(fn (PaymentReceipt $record) => $record->status === 'approved') // Only visible if status is approved
                    ->color('primary'),
            ])
            ->bulkActions([]); // No bulk actions for now
    }

    protected static function createWorkOrder(PaymentReceipt $record): void
    {
        if (WorkOrder::where('payment_receipt_id', $record->id)->exists()) {
            Notification::make()
                ->title(__('Error'))
                ->body(__('A Work Order already exists for this Payment Receipt.'))
                ->danger()
                ->send();
            return;
        }

        WorkOrder::create([
            'inquiry_id' => $record->inquiry_id,
            'payment_receipt_id' => $record->id,
            'invoice_id' => $record->invoice_id,
            'status' => self::DEFAULT_STATUS, // Use default status
        ]);

        Notification::make()
            ->title(__('Success'))
            ->body(__('Work Order created successfully!'))
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
            'index' => Pages\ListPaymentReceipts::route('/'),
            'edit' => Pages\EditPaymentReceipt::route('/{record}/edit'),
        ];
    }
}
