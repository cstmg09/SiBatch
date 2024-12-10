<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use App\Models\PaymentReceipt;
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

use Illuminate\Database\Eloquent\Builder;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationGroup = 'Orders Management';
    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false; // Disable the "Create Purchase Order" button
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
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
                PurchaseOrder::whereHas('inquiry') // Ensures the purchase order has an associated inquiry
                    ->whereHas('invoice')         // Ensures the purchase order has an associated invoice
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Purchase Order ID')
                    ->sortable(),

                TextColumn::make('inquiry.name')
                    ->label('Name'),

                TextColumn::make('inquiry.company')
                    ->label('Company'),

                TextColumn::make('inquiry.address')
                    ->label('Address'),

                TextColumn::make('inquiry.phone')
                    ->label('Phone'),

                TextColumn::make('inquiry.message')
                    ->label('Message'),

                TextColumn::make('invoice.created_at')
                    ->label('Order Date')
                    ->date(),

                TextColumn::make('send_date')
                    ->label('Send Date')
                    ->date(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                    ]),

                Action::make('Make Payment Receipt')
                    ->form([
                        DatePicker::make('payment_date')
                            ->label('Payment Date')
                            ->required(),

                        FileUpload::make('payment_proof')
                            ->label('Payment Proof')
                            ->image()
                            ->directory('payment_proof')  // Save in storage/app/public/products
                            ->nullable(),
                    ])
                    ->action(function (PurchaseOrder $record, array $data) {
                        // Check if a Payment Receipt already exists
                        if (PaymentReceipt::where('purchase_order_id', $record->id)->exists()) {
                            Notification::make()
                                ->title('Error')
                                ->body('A Payment Receipt already exists for this Purchase Order.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Create the Payment Receipt
                        PaymentReceipt::create([
                            'purchase_order_id' => $record->id,
                            'invoice_id' => $record->invoice_id,
                            'inquiry_id' => $record->inquiry_id,
                            'payment_date' => $data['payment_date'],
                            'payment_proof' => $data['payment_proof'],
                            'status' => 'pending', // Default status for Payment Receipt
                        ]);

                        Notification::make()
                            ->title('Success')
                            ->body('Payment Receipt created successfully!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (PurchaseOrder $record) => $record->status === 'approved') // Only visible if status is approved
                    ->color('primary'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
