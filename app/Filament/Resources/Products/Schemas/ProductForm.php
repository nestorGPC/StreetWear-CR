<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('₡')
                    ->required()
                    ->minValue(0),

                TextInput::make('stock')
                    ->label('Stock')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0),

                FileUpload::make('image')
                    ->label('Imagen')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public'),

                Toggle::make('active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}