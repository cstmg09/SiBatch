<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;



class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Product Name')
                    ->required(),
                Textarea::make('description')
                    ->nullable()
                    ->label('Description'),
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required(),
                TextInput::make('stock')
                    ->label('Stock Quantity')
                    ->numeric()
                    ->required(),
                Checkbox::make('is_available')
                    ->label('Is Available')
                    ->default(true),
                FileUpload::make('image')
                    ->label('Product Image')
                    ->image()
                    ->directory('products')  // Save in storage/app/public/products
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Product Name'),
                TextColumn::make("description")
                    ->label("Description"),
                TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->getStateUsing(fn(Product $record) => 'IDR ' . number_format($record->price, 2, ',', '.')),
                TextColumn::make('stock')
                    ->label('Stock Quantity'),
                TextColumn::make('is_available')
                    ->sortable()
                    ->label('Available')
                    ->getStateUsing(fn(Product $record) => $record->is_available ? 'Yes' : 'No'),
                TextColumn::make('image')
                    ->label('Image')
                    ->getStateUsing(function (Product $record) {
                        return $record->image ? '<img src="' . Storage::url($record->image) . '" width="100" />' : null;
                    })
                    ->html()  // This will render the image tag
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
