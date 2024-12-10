<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiriesResource\Pages;
use App\Models\Inquiries;
use App\Models\Product;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;

class InquiriesResource extends Resource
{
    protected static ?string $model = Inquiries::class;

    protected static ?string $navigationGroup = 'Orders Management';
    protected static ?int $navigationSort = 2;


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('company')
                    ->label('Company Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Phone Number')
                    ->required()
                    ->maxLength(20),

                TextInput::make('address')
                    ->label('Address')
                    ->required()
                    ->maxLength(500),

                Textarea::make('message')
                    ->label('Message')
                    ->nullable()
                    ->maxLength(1000),

                Repeater::make('products')
                ->label('Products in Inquiry')
                ->schema([
                    Select::make('product_id')
                        ->label('Product')
                        ->options(Product::pluck('name', 'id'))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            // Fetch price dynamically based on the selected product
                            $product = Product::find($state);
                            $set('price', $product ? $product->price : 0);

                            // Recalculate total dynamically
                            $products = $get('../../products') ?? [];
                            $total = collect($products)->sum(function ($product) {
                                return ($product['price'] ?? 0) * ($product['quantity'] ?? 0);
                            });
                            $set('../../total', $total);
                        }),

                    TextInput::make('price')
                        ->label('Price')
                        ->numeric()
                        ->disabled(), // Auto-filled from the selected product

                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(1)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            // Recalculate total dynamically
                            $products = $get('../../products') ?? [];
                            $total = collect($products)->sum(function ($product) {
                                return ($product['price'] ?? 0) * ($product['quantity'] ?? 0);
                            });
                            $set('../../total', $total);
                        }),
                ])
                ->columns(2),

                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->reactive(),

                Select::make('inquiries_status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->disabled(fn ($record) => in_array($record?->inquiries_status, ['approved', 'rejected'])) // Disable if approved or rejected
                    ->required(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->query(Inquiries::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('company')
                    ->label('Company Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('address')
                    ->label('Address')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('message')
                    ->label('Message')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('idr')
                    ->sortable(),

                BadgeColumn::make('inquiries_status')
                    ->sortable()
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->actions([
                Action::make('Make Invoice')
                    ->action(function (Inquiries $record) {
                        if (Invoice::where('inquiry_id', $record->id)->exists()) {
                            Notification::make()
                                ->title('Invoice already exists for this inquiry.')
                                ->danger()
                                ->send();
                            return;
                        }

                        Invoice::create([
                            'inquiry_id' => $record->id,
                            'customer_id' => ucfirst($record->name) . $record->id,
                            'status' => 'pending',
                        ]);

                        Notification::make()
                            ->title('Invoice created successfully!')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Inquiries $record) => $record->inquiries_status === 'approved')
                    ->requiresConfirmation()
                    ->color('primary'),

                EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInquiries::route('/'),
            'create' => Pages\CreateInquiries::route('/create'),
            'edit' => Pages\EditInquiries::route('/{record}/edit'),
        ];
    }
}
