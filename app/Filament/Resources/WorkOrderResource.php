<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderResource\Pages;
use App\Models\WorkOrder;
use App\Models\PaymentReceipt;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $navigationGroup = 'Orders Management';
    protected static ?int $navigationSort = 6;
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
                WorkOrder::whereHas('inquiry') // Ensures the purchase order has an associated inquiry
                    ->whereHas('invoice')         // Ensures the purchase order has an associated invoice
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Work Order ID')
                    ->sortable(),

                TextColumn::make('inquiry.name')
                    ->label('Customer Name'),

                TextColumn::make('paymentReceipt.id')
                    ->label('Payment Receipt ID'),

                TextColumn::make('invoice.id')
                    ->label('Invoice ID'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                Action::make('Download PDF')
                ->color('success') // Green button
                ->url(fn (WorkOrder $record) => route('work-orders.pdf', $record->id)) // Generate URL for PDF download
                ->openUrlInNewTab() // Open in a new tab
                ->visible(fn (WorkOrder $record) => $record->status === 'approved'), // Show only if the status is approved
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
            'index' => Pages\ListWorkOrders::route('/'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
